<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => ucwords($this->faker->unique()->city()),
            'latitude' => $this->faker->latitude(4, 14),
            'longitude' => $this->faker->latitude(2, 15),
            'is_active' => true,
        ];
    }
}
