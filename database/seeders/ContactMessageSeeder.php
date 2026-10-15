<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        ContactMessage::factory()->count(12)->create([
            'status' => 'new',
        ]);

        ContactMessage::factory()->count(4)->create([
            'status' => 'replied',
            'admin_note' => 'Answered by support.',
        ]);
    }
}
