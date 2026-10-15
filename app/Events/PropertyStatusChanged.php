<?php

namespace App\Events;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Property $property,
        public readonly string $action, // approved | rejected | featured | unfeatured
        public readonly ?User $actor = null,
        public readonly ?string $reason = null,
    ) {}
}
