<?php

namespace App\Services;

use App\Enums\InquiryStatus;
use App\Enums\TourStatus;
use App\Events\InquiryCreated;
use App\Events\InquiryReplied;
use App\Events\TourScheduled;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\TourRequest;
use App\Models\User;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use Illuminate\Validation\ValidationException;

class InquiryService
{
    public function __construct(
        private readonly InquiryRepositoryInterface $inquiries,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createInquiry(array $data, ?User $user = null): Inquiry
    {
        /** @var Property $property */
        $property = Property::query()->findOrFail($data['property_id']);

        if (! $property->is_approved) {
            throw ValidationException::withMessages([
                'property_id' => 'This listing is not available.',
            ]);
        }

        $inquiry = $this->inquiries->create([
            'property_id' => $property->id,
            'user_id' => $user?->id,
            'agent_id' => $property->agent_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'preferred_tour_date' => $data['preferred_tour_date'] ?? null,
            'status' => InquiryStatus::New,
        ]);

        event(new InquiryCreated($inquiry));

        return $inquiry;
    }

    public function reply(Inquiry $inquiry, string $message, User $agent): Inquiry
    {
        $inquiry = $this->inquiries->reply($inquiry, $message, $agent);

        event(new InquiryReplied($inquiry));

        return $inquiry;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function scheduleTour(array $data, User $user): TourRequest
    {
        /** @var Property $property */
        $property = Property::query()->findOrFail($data['property_id']);

        $tour = TourRequest::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'agent_id' => $property->agent_id,
            'scheduled_at' => $data['scheduled_at'],
            'status' => TourStatus::Requested,
            'notes' => $data['notes'] ?? null,
        ]);

        event(new TourScheduled($tour));

        return $tour;
    }
}
