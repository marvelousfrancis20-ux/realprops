<?php

namespace App\Listeners;

use App\Events\InquiryReplied;
use App\Notifications\InquiryRepliedNotification;

class NotifyUserOfInquiryReply
{
    public function handle(InquiryReplied $event): void
    {
        $user = $event->inquiry->user;

        $user?->notify(new InquiryRepliedNotification($event->inquiry));
    }
}
