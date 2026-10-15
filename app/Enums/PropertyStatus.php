<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Available = 'available';
    case Pending = 'pending';
    case Sold = 'sold';
    case Rented = 'rented';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::Pending => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            self::Sold => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
            self::Rented => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
        };
    }

    /**
     * @return list<string>
     */
    public static function options(): array
    {
        return array_column(self::cases(), 'value');
    }
}
