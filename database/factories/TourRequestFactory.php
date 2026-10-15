<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\TourRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourRequestFactory extends Factory
{
    protected $model = TourRequest::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'agent_id' => User::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'status' => 'requested',
            'notes' => $this->faker->sentence(12),
        ];
    }
}
