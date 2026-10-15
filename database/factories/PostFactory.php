<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Support\PhotoPool;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'title' => ucwords($this->faker->unique()->words(6, true)),
            'excerpt' => $this->faker->sentence(14),
            'content' => implode("\n\n", $this->faker->paragraphs(6)),
            'featured_image' => PhotoPool::blog($this->faker->unique()->numberBetween(1, 100000)),
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year'),
            'meta_title' => null,
            'meta_description' => $this->faker->sentence(16),
            'views_count' => $this->faker->numberBetween(0, 3000),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
