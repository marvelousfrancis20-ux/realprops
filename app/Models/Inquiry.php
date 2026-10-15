<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'property_id',
        'user_id',
        'agent_id',
        'name',
        'email',
        'phone',
        'message',
        'preferred_tour_date',
        'status',
        'reply_message',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_tour_date' => 'date',
            'replied_at' => 'datetime',
            'status' => InquiryStatus::class,
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function isUnread(): bool
    {
        return $this->status === InquiryStatus::New;
    }
}
