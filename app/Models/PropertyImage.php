<?php

namespace App\Models;

use App\Support\PhotoPool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'property_id',
        'path',
        'url',
        'alt',
        'caption',
        'sort_order',
        'is_cover',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_cover' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (filled($this->url)) {
            return $this->url;
        }

        if (filled($this->path)) {
            return Storage::disk($this->disk ?? 'public')->url($this->path);
        }

        $property = filled($this->property_id) ? Property::query()->find($this->property_id) : null;

        if ($property) {
            return $property->photoUrl((int) ($this->sort_order ?? 0));
        }

        return PhotoPool::forLabel(null, $this->id ?? 'property-image', (int) ($this->sort_order ?? 0));
    }

    public function altText(): string
    {
        return $this->alt ?: ($this->property?->title ?: 'Property image');
    }
}
