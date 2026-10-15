<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->numerify('0##########'),
            'whatsapp' => fake()->numerify('0##########'),
            'bio' => fake()->paragraph(),
            'is_suspended' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name(),
        ])->afterCreating(function ($user) {
            $user->syncRoles([UserRole::Admin->value]);
        });
    }

    public function agent(): static
    {
        return $this->afterCreating(function ($user) {
            $user->syncRoles([UserRole::Agent->value]);
            $user->ensureAgentProfile();
        });
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => 'Seeded suspended account',
        ]);
    }
}
