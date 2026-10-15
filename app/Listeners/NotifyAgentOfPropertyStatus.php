<?php

namespace App\Listeners;

use App\Events\PropertyStatusChanged;
use App\Notifications\PropertyStatusNotification;

class NotifyAgentOfPropertyStatus
{
    public function handle(PropertyStatusChanged $event): void
    {
        $event->property->agent?->notify(
            new PropertyStatusNotification($event->property, $event->action, $event->reason)
        );
    }
}
