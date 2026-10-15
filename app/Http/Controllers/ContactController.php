<?php

namespace App\Http\Controllers;

use App\Enums\ContactStatus;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function __construct(private readonly RecaptchaService $recaptcha) {}

    /**
     * Working contact form (rate limited + optional reCAPTCHA v3).
     */
    public function store(StoreContactRequest $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            // Honeypot triggered - silently accept and drop.
            return back()->with('status', 'Thanks for reaching out!');
        }

        if (! $this->recaptcha->passes($request)) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Please confirm you are not a robot.'])
                ->withInput();
        }

        $message = ContactMessage::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->input('phone'),
            'subject' => $request->string('subject')->toString(),
            'message' => $request->string('message')->toString(),
            'status' => ContactStatus::New,
        ]);

        Notification::route('mail', config('mail.from.address'))
            ->notify(new ContactMessageReceived($message));

        return back()->with('status', 'Thanks! Your message has been sent - we will get back to you shortly.');
    }
}
