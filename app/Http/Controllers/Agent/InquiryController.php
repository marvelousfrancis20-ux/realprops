<?php

namespace App\Http\Controllers\Agent;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inquiry\ReplyInquiryRequest;
use App\Models\Inquiry;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use App\Services\InquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function __construct(
        private readonly InquiryRepositoryInterface $repository,
        private readonly InquiryService $service,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');

        return view('agent.inquiries.index', [
            'inquiries' => $this->repository->paginateForAgent($request->user(), $status),
            'counts' => [
                'all' => $request->user()->receivedInquiries()->count(),
                'new' => $request->user()->receivedInquiries()->where('status', InquiryStatus::New)->count(),
                'read' => $request->user()->receivedInquiries()->where('status', InquiryStatus::Read)->count(),
                'replied' => $request->user()->receivedInquiries()->where('status', InquiryStatus::Replied)->count(),
                'archived' => $request->user()->receivedInquiries()->where('status', InquiryStatus::Archived)->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function show(Request $request, Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        if ($inquiry->isUnread()) {
            $this->repository->markStatus($inquiry, InquiryStatus::Read->value);
        }

        return view('agent.inquiries.show', [
            'inquiry' => $inquiry->load(['property.images', 'user']),
        ]);
    }

    public function reply(ReplyInquiryRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('reply', $inquiry);

        $this->service->reply($inquiry, $request->validated('reply_message'), $request->user());

        return redirect()
            ->route('agent.inquiries.show', $inquiry)
            ->with('status', 'Reply sent to '.$inquiry->name.'.');
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('update', $inquiry);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(InquiryStatus::cases(), 'value'))],
        ]);

        $this->repository->markStatus($inquiry, $data['status']);

        return back()->with('status', 'Inquiry marked as '.$data['status'].'.');
    }

    public function destroy(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()->route('agent.inquiries.index')->with('status', 'Inquiry deleted.');
    }
}
