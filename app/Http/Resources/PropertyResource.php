<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => $this->formattedPrice(),
            'currency' => $this->currency,
            'listing_type' => $this->listing_type?->value ?? '',
            'status' => $this->status?->value ?? '',
            'property_type' => new PropertyTypeResource($this->whenLoaded('type')),
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'toilets' => $this->toilets,
            'square_meters' => $this->square_meters,
            'year_built' => $this->year_built,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'is_featured' => (bool) $this->is_featured,
            'is_approved' => (bool) $this->is_approved,
            'views_count' => $this->views_count,
            'video_url' => $this->video_url,
            'virtual_tour_url' => $this->virtual_tour_url,
            'cover_image' => $this->imageUrl(),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
            'agent' => new AgentResource($this->whenLoaded('agent')),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'url' => route('properties.show', ['property' => $this->slug]),
        ];
    }
}
