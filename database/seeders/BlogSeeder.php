<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Buying', 'Selling', 'Renting', 'Investing', 'Market News', 'Guides'])
            ->map(fn (string $name) => Category::firstOrCreate(
                ['name' => $name],
                ['description' => "{$name} articles"]
            ));

        $tags = collect(['mortgage', 'lga', 'rent', 'buy', 'investment', 'tips', 'legal', 'inspection', 'luxury', 'budget', 'market', 'relocation'])
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name]));

        $authors = User::role('agent')->take(10)->get();

        if ($authors->isEmpty()) {
            $authors = User::all();
        }

        Post::factory()
            ->count(20)
            ->state(fn () => [
                'author_id' => $authors->random()->id,
                'category_id' => $categories->random()->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags) {
                $post->tags()->sync($tags->random(rand(1, 3))->pluck('id')->all());
            });

        Post::factory()->count(3)->draft()->state(fn () => [
            'author_id' => $authors->random()->id,
            'category_id' => $categories->random()->id,
        ])->create();
    }
}
