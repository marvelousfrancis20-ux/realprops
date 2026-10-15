<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = ContactMessage::query()->latest();

        if (filled($status)) {
            $query->where('status', $status);
        }

        return view('admin.contact-messages.index', [
            'messages' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => ContactMessage::count(),
                'new' => ContactMessage::where('status', 'new')->count(),
                'read' => ContactMessage::where('status', 'read')->count(),
                'replied' => ContactMessage::where('status', 'replied')->count(),
                'closed' => ContactMessage::where('status', 'closed')->count(),
            ],
            'activeStatus' => $status,
        ]);
    }

    public function show(ContactMessage $contact_message): View
    {
        if ($contact_message->status === ContactStatus::New) {
            $contact_message->update(['status' => ContactStatus::Read]);
        }

        return view('admin.contact-messages.show', [
            'message' => $contact_message,
        ]);
    }

    public function update(Request $request, ContactMessage $contact_message): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,replied,closed'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $contact_message->update($data);

        return back()->with('status', 'Message updated.');
    }

    public function destroy(ContactMessage $contact_message): RedirectResponse
    {
        $contact_message->delete();

        return redirect()->route('admin.contact-messages.index')->with('status', 'Message deleted.');
    }
}
