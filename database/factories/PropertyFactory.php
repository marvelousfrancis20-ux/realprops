<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $listingType = $this->faker->randomElement(['sale', 'rent']);
        $city = $this->faker->randomElement([
            ['Lagos', 'Lagos'],
            ['Abuja', 'FCT'],
            ['Port Harcourt', 'Rivers'],
            ['Kano', 'Kano'],
            ['Ibadan', 'Oyo'],
            ['Enugu', 'Enugu'],
            ['Benin City', 'Edo'],
            ['Kaduna', 'Kaduna'],
            ['Uyo', 'Akwa Ibom'],
            ['Calabar', 'Cross River'],
        ]);

        return [
            'agent_id' => User::factory(),
            'property_type_id' => PropertyType::factory(),
            'city_id' => City::factory(),
            'title' => ucwords($this->faker->unique()->words(3, true)).' '.$this->faker->randomElement([
                'Apartment', 'Villa', 'Duplex', 'Bungalow', 'Studio', 'Penthouse', 'Terrace', 'Mansion', 'Mini Flat',
            ]),
            'description' => $this->faker->paragraphs(4, true),
            'price' => $listingType === 'rent'
                ? $this->faker->numberBetween(100000, 3000000)
                : $this->faker->numberBetween(5000000, 250000000),
            'currency' => 'NGN',
            'listing_type' => $listingType,
            'status' => 'available',
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'toilets' => $this->faker->numberBetween(1, 6),
            'square_meters' => $this->faker->numberBetween(40, 600),
            'year_built' => $this->faker->numberBetween(1990, 2024),
            'is_featured' => false,
            'is_approved' => true,
            'address' => $this->faker->streetAddress(),
            'city' => $city[0],
            'state' => $city[1],
            'country' => 'Nigeria',
            'latitude' => $this->faker->latitude(4, 14),
            'longitude' => $this->faker->latitude(2, 15),
            'views_count' => $this->faker->numberBetween(0, 5000),
            'published_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }

    public function forSale(): static
    {
        return $this->state(fn () => ['listing_type' => 'sale']);
    }

    public function forRent(): static
    {
        return $this->state(fn () => ['listing_type' => 'rent']);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'is_approved' => false,
            'published_at' => null,
        ]);
    }
}
