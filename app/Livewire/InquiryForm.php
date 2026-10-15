<?php

namespace App\Livewire;

use App\Models\Property;
use App\Services\InquiryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InquiryForm extends Component
{
    public Property $property;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public ?string $preferredTourDate = null;

    public bool $isTour = false;

    public ?string $success = null;

    public function mount(Property $property): void
    {
        $this->property = $property;

        if (Auth::check()) {
            $this->name = Auth::user()->name ?? '';
            $this->email = Auth::user()->email ?? '';
            $this->phone = (string) (Auth::user()->phone ?? '');
        }
    }

    public function submit(InquiryService $service): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'max:2000'],
            'preferredTourDate' => ['nullable', 'date', 'after:today'],
        ]);

        $service->createInquiry([
            'property_id' => $this->property->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'message' => $this->message,
            'preferred_tour_date' => $this->preferredTourDate,
        ], Auth::user());

        $this->success = $this->isTour
            ? 'Tour request sent! The agent will confirm your preferred date.'
            : 'Message sent to the agent. You will get a reply by e-mail.';

        $this->reset('message', 'preferredTourDate');
    }

    public function render()
    {
        return view('livewire.inquiry-form');
    }
}
