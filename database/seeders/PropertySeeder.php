<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\TourRequest;
use App\Models\User;
use App\Support\PhotoPool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $agents = User::role('agent')->get();
        $types = PropertyType::all();
        $amenities = Amenity::all();

        if ($agents->isEmpty()) {
            return;
        }

        $titleFor = fn (string $type): string => $this->propertyTitle($type);

        Property::factory()
            ->count(200)
            ->state(function () use ($agents, $types, $titleFor) {
                $type = $types->random();

                return [
                    'agent_id' => $agents->random()->id,
                    'property_type_id' => $type->id,
                    'city_id' => null,
                    'title' => $titleFor($type->name),
                ];
            })
            ->create()
            ->each(function (Property $property) use ($amenities) {
                $this->createPropertyImages($property);

                $property->amenities()->sync(
                    $amenities->random(rand(3, 8))->pluck('id')->all()
                );
            });

        // A handful of pending listings so the admin queue has work.
        Property::factory()
            ->count(12)
            ->pending()
            ->state(function () use ($agents, $types, $titleFor) {
                $type = $types->random();

                return [
                    'agent_id' => $agents->random()->id,
                    'property_type_id' => $type->id,
                    'city_id' => null,
                    'title' => $titleFor($type->name),
                ];
            })
            ->create()
            ->each(fn (Property $property) => $this->createPropertyImages($property));

        // Feature a few live listings.
        Property::query()
            ->where('is_approved', true)
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->each(fn (Property $property) => $property->update(['is_featured' => true]));

        $this->seedEngagement($agents);
    }

    private function propertyTitle(string $type): string
    {
        $prefix = ucwords(fake()->words(3, true));

        return $type === 'Land'
            ? $prefix.' Plot of Land'
            : $prefix.' '.$type;
    }

    private function createPropertyImages(Property $property): void
    {
        $pool = $property->photoPool();
        $extras = rand(2, 4);

        foreach (range(0, $extras) as $sortOrder) {
            PropertyImage::factory()->create([
                'property_id' => $property->id,
                'is_cover' => $sortOrder === 0,
                'sort_order' => $sortOrder,
                'url' => PhotoPool::url($pool, $property->id, $sortOrder),
                'alt' => $property->title,
            ]);
        }
    }

    /**
     * @param  Collection<int, User>  $agents
     */
    private function seedEngagement($agents): void
    {
        $users = User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['admin', 'agent']))
            ->take(30)
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $properties = Property::where('is_approved', true)->inRandomOrder()->take(60)->get();

        foreach ($properties as $index => $property) {
            if ($index % 3 === 0) {
                $user = $users->random();

                if (! $user->favorites()->where('property_id', $property->id)->exists()) {
                    $user->favorites()->create(['property_id' => $property->id]);
                }
            }

            if ($index % 2 === 0) {
                $user = $users->random();

                Inquiry::factory()->create([
                    'property_id' => $property->id,
                    'user_id' => $user->id,
                    'agent_id' => $property->agent_id,
                    'status' => $index % 4 === 0 ? 'replied' : 'new',
                    'reply_message' => $index % 4 === 0 ? 'Thanks for reaching out, viewing is available this weekend.' : null,
                    'replied_at' => $index % 4 === 0 ? now()->subDay() : null,
                ]);
            }

            if ($index % 4 === 0) {
                $user = $users->random();

                TourRequest::factory()->create([
                    'property_id' => $property->id,
                    'user_id' => $user->id,
                    'agent_id' => $property->agent_id,
                    'status' => ['requested', 'confirmed', 'completed', 'cancelled'][array_rand(['requested', 'confirmed', 'completed', 'cancelled'])],
                ]);
            }
        }
    }
}
