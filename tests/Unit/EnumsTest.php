<?php

use App\Enums\InquiryStatus;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\TourStatus;
use App\Enums\UserRole;

it('returns human labels for listing types', function () {
    expect(ListingType::Sale->label())->toBe('For Sale')
        ->and(ListingType::Rent->label())->toBe('For Rent')
        ->and(ListingType::ShortLet->label())->toBe('Short Let');
});

it('flags rental listing types', function () {
    expect(ListingType::Rent->isRental())->toBeTrue()
        ->and(ListingType::ShortLet->isRental())->toBeTrue()
        ->and(ListingType::Sale->isRental())->toBeFalse();
});

it('exposes listing type option values', function () {
    expect(ListingType::options())->toBe(['sale', 'rent', 'short_let']);
});

it('labels property statuses', function () {
    expect(PropertyStatus::Available->label())->toBe('Available')
        ->and(PropertyStatus::Sold->label())->toBe('Sold');
});

it('lists property status options', function () {
    expect(PropertyStatus::options())->toBe(['available', 'pending', 'sold', 'rented']);
});

it('derives permissions per role', function () {
    expect(UserRole::User->permissions())->toBe(['properties.view'])
        ->and(UserRole::Agent->permissions())->toContain('properties.create', 'properties.delete')
        ->and(UserRole::Admin->permissions())->toContain('users.manage', 'agents.verify', 'reports.view', 'settings.manage');
});

it('labels roles for the UI', function () {
    expect(UserRole::User->label())->toBe('Member')
        ->and(UserRole::Agent->label())->toBe('Agent')
        ->and(UserRole::Admin->label())->toBe('Administrator');
});

it('has inquiry and tour status cases', function () {
    expect(InquiryStatus::cases())->not->toBeEmpty()
        ->and(TourStatus::cases())->not->toBeEmpty();
});
