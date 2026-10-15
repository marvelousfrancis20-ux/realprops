<?php

namespace App\Events;

use App\Models\Inquiry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InquiryReplied
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Inquiry $inquiry) {}
}
