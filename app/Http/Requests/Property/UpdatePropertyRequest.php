<?php

namespace App\Http\Requests\Property;

use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('property')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:40', 'max:20000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'listing_type' => ['required', Rule::enum(ListingType::class)],
            'property_type_id' => ['required', 'integer', 'exists:property_types,id'],
            'status' => ['nullable', Rule::enum(PropertyStatus::class)],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:100'],
            'bathrooms' => ['required', 'integer', 'min:0', 'max:100'],
            'toilets' => ['required', 'integer', 'min:0', 'max:100'],
            'square_meters' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:'.(now()->year + 1)],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'virtual_tour_url' => ['nullable', 'url', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp,avif', 'max:5120'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer', 'exists:property_images,id'],
            'cover_index' => ['nullable', 'integer', 'min:0'],
            'image_alts' => ['nullable', 'array'],
            'image_alts.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
