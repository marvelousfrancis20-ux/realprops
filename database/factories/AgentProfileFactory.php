<?php

namespace Database\Factories;

use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentProfileFactory extends Factory
{
    protected $model = AgentProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'agency_name' => ucwords($this->faker->unique()->company()).' Realty',
            'license_number' => strtoupper($this->faker->unique()->bothify('LIC-####??')),
            'years_experience' => $this->faker->numberBetween(0, 25),
            'bio' => $this->faker->paragraph(3),
            'whatsapp' => $this->faker->numerify('0##########'),
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'verified_at' => $this->faker->dateTimeBetween('-1 year'),
            'verified_by' => null,
        ]);
    }
}
