<?php

namespace App\Repositories\Contracts;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InquiryRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Inquiry;

    public function paginateForAgent(User $agent, ?string $status = null, int $perPage = 15): LengthAwarePaginator;

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function reply(Inquiry $inquiry, string $message, User $agent): Inquiry;

    public function markStatus(Inquiry $inquiry, string $status): Inquiry;
}
