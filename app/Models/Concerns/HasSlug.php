<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Automatically generates a unique, human readable slug on create.
 *
 * Override slugSource() on the model to change the source column (defaults to name).
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (self $model): void {
            if ($model->slug === null || $model->slug === '') {
                $model->slug = $model->generateUniqueSlug(
                    (string) $model->getAttribute($model->slugSource())
                );
            }
        });
    }

    public function slugSource(): string
    {
        return 'name';
    }

    public function generateUniqueSlug(string $value): string
    {
        $base = Str::slug($value);

        if ($base === '') {
            $base = 'item';
        }

        $query = static::query();

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        $slug = $base;
        $suffix = 1;

        while ($query->clone()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
