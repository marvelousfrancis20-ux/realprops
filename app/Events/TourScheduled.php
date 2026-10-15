<?php

namespace App\Events;

use App\Models\TourRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TourScheduled
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly TourRequest $tourRequest) {}
}
