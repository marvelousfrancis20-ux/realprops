<?php

namespace App\Models;

use App\Enums\ContactStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status', 'admin_note',
    ];

    protected function casts(): array
    {
        return ['status' => ContactStatus::class];
    }
}
