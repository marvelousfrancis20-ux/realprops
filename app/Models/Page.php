<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasSlug;

    /** @var list<string> */
    protected $fillable = [
        'slug', 'title', 'content', 'meta_title', 'meta_description', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function slugSource(): string
    {
        return 'title';
    }
}
