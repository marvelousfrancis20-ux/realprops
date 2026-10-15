<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, LogsActivity, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'whatsapp',
        'bio',
        'avatar_path',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
        'email_verified_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_suspended'])
            ->logOnlyDirty();
    }

    public function ensureAgentProfile(): AgentProfile
    {
        return $this->agentProfile()->firstOrCreate([]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function agentProfile(): HasOne
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'user_id');
    }

    public function sentInquiries(): HasMany
    {
        return $this->inquiries();
    }

    public function receivedInquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'agent_id');
    }

    public function tourRequests(): HasMany
    {
        return $this->hasMany(TourRequest::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin->value);
    }

    public function isAgent(): bool
    {
        return $this->hasRole(UserRole::Agent->value);
    }

    /**
     * The primary role name of the user (admin/agent/user).
     *
     * Note: Spatie's `role()` query scope remains available statically
     * (e.g. `User::role('agent')`); this helper is the instance-level view.
     */
    public function primaryRole(): string
    {
        if ($this->isAdmin()) {
            return UserRole::Admin->value;
        }

        if ($this->isAgent()) {
            return UserRole::Agent->value;
        }

        return UserRole::User->value;
    }

    public function dashboardUrl(): string
    {
        return match ($this->primaryRole()) {
            UserRole::Admin->value => route('admin.dashboard'),
            UserRole::Agent->value => route('agent.dashboard'),
            default => route('dashboard'),
        };
    }

    public function isVerifiedAgent(): bool
    {
        return $this->isAgent() && $this->agentProfile?->verified_at !== null;
    }

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    /**
     * @return Collection<int, int>
     */
    public function favoritePropertyIds(): Collection
    {
        return $this->favorites()->pluck('property_id');
    }

    public function isFavorite(Property $property): bool
    {
        return $this->favorites()->where('property_id', $property->id)->exists();
    }

    public function hasFavorited(Property $property): bool
    {
        return $this->isFavorite($property);
    }

    /**
     * Number of listings the user may keep live (0 = unlimited).
     */
    public function listingLimit(): int
    {
        $subscription = $this->subscriptions()
            ->where('stripe_status', 'active')
            ->whereNotNull('plan_id')
            ->with('plan')
            ->first();

        if ($subscription?->plan) {
            return (int) $subscription->plan->listing_limit;
        }

        return 3; // free tier
    }

    public function allowsFeaturedListings(): bool
    {
        $subscription = $this->subscriptions()
            ->where('stripe_status', 'active')
            ->whereNotNull('plan_id')
            ->with('plan')
            ->first();

        return (bool) $subscription?->plan?->allows_featured;
    }

    public function canPublishProperty(): bool
    {
        $limit = $this->listingLimit();

        if ($limit === 0) {
            return true;
        }

        return $this->properties()->count() < $limit;
    }

    public function initials(): string
    {
        $parts = collect(explode(' ', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)));

        return $parts->implode('') ?: 'U';
    }

    public function avatarUrl(): ?string
    {
        $avatar = $this->getFirstMedia('avatar');

        if ($avatar) {
            return $avatar->getUrl();
        }

        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->nonQueued();
    }
}
