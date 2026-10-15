<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, HasSlug;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_interval',
        'stripe_price_id',
        'listing_limit',
        'allows_featured',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'listing_limit' => 'integer',
            'allows_featured' => 'boolean',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isFree(): bool
    {
        return (float) $this->price === 0.0;
    }

    public function isUnlimited(): bool
    {
        return (int) $this->listing_limit === 0;
    }

    public function priceLabel(): string
    {
        return Property::currencySymbol($this->currency).number_format((float) $this->price, 0)
            .'/'.$this->billing_interval;
    }
}
