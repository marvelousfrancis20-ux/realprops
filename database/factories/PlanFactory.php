<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->unique()->words(2, true)).' Plan',
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomElement([0, 2500, 7500, 15000]),
            'currency' => 'NGN',
            'billing_interval' => $this->faker->randomElement(['month', 'year']),
            'stripe_price_id' => null,
            'listing_limit' => $this->faker->randomElement([3, 10, 50, 0]),
            'allows_featured' => $this->faker->boolean(40),
            'features' => $this->faker->randomElements(['Featured listings', 'Priority support', 'Unlimited photos', 'Analytics'], 2),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'name' => 'Free',
            'price' => 0,
            'listing_limit' => 3,
            'allows_featured' => false,
        ]);
    }
}
