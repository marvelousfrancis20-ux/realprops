<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tour\StoreTourRequestRequest;
use App\Services\InquiryService;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function __construct(
        private readonly InquiryService $service,
        private readonly RecaptchaService $recaptcha,
    ) {}

    /**
     * The "sent inquiries" inbox for the authenticated buyer/renter.
     */
    public function index(Request $request)
    {
        return view('inquiries', [
            'inquiries' => $request->user()->inquiries()
                ->with(['property.images', 'agent'])
                ->latest()
                ->paginate(10),
        ]);
    }

    /**
     * Store an inquiry sent from a property page (rate limited + honeypot).
     */
    public function store(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with('status', 'Thanks! The agent will be in touch.');
        }

        $data = $request->validate([
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'preferred_tour_date' => ['nullable', 'date', 'after:today'],
        ]);

        if (! $this->recaptcha->passes($request)) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Please confirm you are not a robot.'])
                ->withInput();
        }

        $this->service->createInquiry($data, $request->user());

        return back()->with('status', 'Your inquiry has been sent to the agent.');
    }

    /**
     * Schedule a property tour (calendar picker).
     */
    public function storeTour(StoreTourRequestRequest $request): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('intended', url()->previous());
        }

        if (filled($request->input('website'))) {
            return back()->with('status', 'Tour request received.');
        }

        $this->service->scheduleTour($request->validated(), $request->user());

        return back()->with('status', 'Tour request sent! The agent will confirm shortly.');
    }
}
