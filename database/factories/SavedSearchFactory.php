<?php

namespace Database\Factories;

use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedSearchFactory extends Factory
{
    protected $model = SavedSearch::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => ucwords($this->faker->words(3, true)),
            'filters' => [
                'listing_type' => $this->faker->randomElement(['sale', 'rent']),
                'bedrooms' => $this->faker->numberBetween(1, 4),
            ],
            'notify' => true,
            'matches_count' => 0,
        ];
    }
}
