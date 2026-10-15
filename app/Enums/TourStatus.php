<?php

namespace App\Enums;

enum TourStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Requested => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            self::Confirmed => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::Completed => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300',
            self::Cancelled => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
        };
    }
}
