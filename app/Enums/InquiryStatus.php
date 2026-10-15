<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::New => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300',
            self::Read => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
            self::Replied => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            self::Archived => 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        };
    }
}
