<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @var list<string> */
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::cached();

        $value = $settings[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        return match ($settings[$key.'_type'] ?? 'string') {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        $stored = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type]
        );

        static::flushCache();
    }

    /**
     * @return array<string, mixed>
     */
    public static function cached(): array
    {
        return Cache::rememberForever('app.settings', function (): array {
            $result = [];

            foreach (static::query()->get() as $setting) {
                $result[$setting->key] = $setting->value;
                $result[$setting->key.'_type'] = $setting->type;
            }

            return $result;
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('app.settings');
    }

    public static function boot(): void
    {
        parent::boot();

        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
