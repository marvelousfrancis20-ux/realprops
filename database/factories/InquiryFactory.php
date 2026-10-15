<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'agent_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('0##########'),
            'message' => $this->faker->paragraph(3),
            'preferred_tour_date' => $this->faker->optional(0.6)->dateTimeBetween('+1 day', '+14 days'),
            'status' => 'new',
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['status' => 'read']);
    }

    public function replied(): static
    {
        return $this->state(fn () => [
            'status' => 'replied',
            'reply_message' => $this->faker->paragraph(2),
            'replied_at' => now(),
        ]);
    }
}
