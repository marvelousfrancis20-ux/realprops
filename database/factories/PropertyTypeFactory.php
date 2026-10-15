<?php

namespace Database\Factories;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyTypeFactory extends Factory
{
    protected $model = PropertyType::class;

    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->unique()->words(2, true)),
            'description' => $this->faker->sentence(6),
            'icon' => $this->faker->randomElement(['🏠', '🏢', '🏡', '🏘️', '🏚️', '🏭']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
