<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerptText(),
            'content' => $this->content,
            'featured_image' => $this->coverUrl(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'author' => [
                'id' => optional($this->author)->id,
                'name' => optional($this->author)->name,
            ],
            'published_at' => $this->published_at?->toIso8601String(),
            'url' => route('blog.show', ['post' => $this->slug]),
        ];
    }
}
