<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => ucwords($this->faker->unique()->words(3, true)),
            'content' => $this->faker->paragraphs(5, true),
            'meta_title' => null,
            'meta_description' => $this->faker->sentence(14),
            'is_active' => true,
        ];
    }
}
