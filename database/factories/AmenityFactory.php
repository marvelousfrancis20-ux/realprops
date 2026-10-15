<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;

class AmenityFactory extends Factory
{
    protected $model = Amenity::class;

    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->unique()->words(2, true)),
            'icon' => $this->faker->randomElement(['📶', '🏊', '🅿️', '🏋️', '🛡️', '🌿', '🍽️', '🧹', '📶', '🪑']),
            'is_active' => true,
        ];
    }
}
