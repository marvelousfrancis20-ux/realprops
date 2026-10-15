<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the full HTTP kernel, so they refresh the database and
| seed the roles/permissions Spatie lookups expect (home stats, role
| middleware, nav visibility, policies).
|
*/

uses(TestCase::class, RefreshDatabase::class)
    ->beforeEach(fn () => $this->seed(RoleSeeder::class))
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
|
| Shortcut factories used across the test suite.
|
*/

function createUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

function createAgent(array $attributes = []): User
{
    return User::factory()->agent()->create($attributes);
}

function createAdmin(array $attributes = []): User
{
    return User::factory()->admin()->create($attributes);
}
