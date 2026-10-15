<?php

namespace App\Listeners;

use App\Events\InquiryCreated;
use App\Notifications\NewInquiryNotification;

class NotifyAgentOfInquiry
{
    public function handle(InquiryCreated $event): void
    {
        $agent = $event->inquiry->agent;

        $agent?->notify(new NewInquiryNotification($event->inquiry));
    }
}
