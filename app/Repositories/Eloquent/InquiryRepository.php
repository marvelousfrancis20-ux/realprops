<?php

namespace App\Repositories\Eloquent;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use App\Models\User;
use App\Repositories\Contracts\InquiryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InquiryRepository implements InquiryRepositoryInterface
{
    public function create(array $data): Inquiry
    {
        return Inquiry::create($data);
    }

    public function paginateForAgent(User $agent, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $agent->receivedInquiries()
            ->with(['property.images', 'user'])
            ->latest();

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->inquiries()
            ->with(['property.images', 'agent'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function reply(Inquiry $inquiry, string $message, User $agent): Inquiry
    {
        $inquiry->update([
            'reply_message' => $message,
            'replied_at' => now(),
            'status' => InquiryStatus::Replied,
        ]);

        return $inquiry;
    }

    public function markStatus(Inquiry $inquiry, string $status): Inquiry
    {
        $inquiry->update(['status' => $status]);

        return $inquiry;
    }
}
