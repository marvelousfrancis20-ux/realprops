<?php

namespace App\Listeners;

use App\Events\TourScheduled;
use App\Notifications\TourRequestNotification;

class NotifyAgentOfTourRequest
{
    public function handle(TourScheduled $event): void
    {
        $event->tourRequest->agent?->notify(new TourRequestNotification($event->tourRequest));
    }
}
