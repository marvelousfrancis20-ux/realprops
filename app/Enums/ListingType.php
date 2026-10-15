<?php

namespace App\Enums;

enum ListingType: string
{
    case Sale = 'sale';
    case Rent = 'rent';
    case ShortLet = 'short_let';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'For Sale',
            self::Rent => 'For Rent',
            self::ShortLet => 'Short Let',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Sale => 'bg-teal-700 text-white',
            self::Rent => 'bg-amber-500 text-white',
            self::ShortLet => 'bg-indigo-600 text-white',
        };
    }

    public function isRental(): bool
    {
        return $this !== self::Sale;
    }

    /**
     * @return list<string>
     */
    public static function options(): array
    {
        return array_column(self::cases(), 'value');
    }
}
