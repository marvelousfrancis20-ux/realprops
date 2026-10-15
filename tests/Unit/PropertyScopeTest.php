<?php

use App\Enums\ListingType;
use App\Models\Property;

it('scopes approved properties', function () {
    Property::factory()->create(['is_approved' => true]);
    Property::factory()->create(['is_approved' => false]);

    expect(Property::approved()->count())->toBe(1);
});

it('scopes available properties', function () {
    Property::factory()->create(['status' => 'available']);
    Property::factory()->create(['status' => 'sold']);

    expect(Property::available()->count())->toBe(1);
});

it('scopes featured properties', function () {
    Property::factory()->create(['is_featured' => true]);
    Property::factory()->create(['is_featured' => false]);

    expect(Property::featured()->count())->toBe(1);
});

it('scopes pending approval properties', function () {
    Property::factory()->create(['is_approved' => false]);
    Property::factory()->create(['is_approved' => true]);

    expect(Property::pendingApproval()->count())->toBe(1);
});

it('scopes by listing type with enum or string', function () {
    Property::factory()->create(['listing_type' => 'sale']);
    Property::factory()->create(['listing_type' => 'rent']);

    expect(Property::forListingType('sale')->count())->toBe(1)
        ->and(Property::forListingType(ListingType::Rent)->count())->toBe(1);
});

it('scopes by price range', function () {
    Property::factory()->create(['price' => 100]);
    Property::factory()->create(['price' => 1000]);
    Property::factory()->create(['price' => 100000]);

    expect(Property::priceBetween(50, 500)->count())->toBe(1);
});

it('scopes by city and state strings', function () {
    Property::factory()->create(['city' => 'Lagos', 'state' => 'Lagos']);
    Property::factory()->create(['city' => 'Abuja', 'state' => 'FCT']);

    expect(Property::inCity('Lagos')->count())->toBe(1)
        ->and(Property::inState('FCT')->count())->toBe(1);
});

it('scopes published properties', function () {
    Property::factory()->create(['published_at' => now()->subDay()]);
    Property::factory()->create(['published_at' => null]);

    expect(Property::published()->count())->toBe(1);
});
