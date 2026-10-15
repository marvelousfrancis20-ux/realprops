<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AgentProfile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'agency_name',
        'license_number',
        'id_document_path',
        'license_document_path',
        'years_experience',
        'bio',
        'whatsapp',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'years_experience' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'verified_at', 'license_number'])
            ->logOnlyDirty();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id', 'user_id');
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
