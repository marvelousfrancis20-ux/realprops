<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@realprops.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '+2348000000001',
                'bio' => 'Platform administrator.',
                'email_verified_at' => now(),
            ]
        )->syncRoles(['admin']);

        if (User::role('admin')->count() < 2) {
            User::factory()->count(4)->admin()->create();
        }

        if (! User::role('agent')->exists()) {
            User::factory()->count(20)->agent()->create();
        }

        if (! User::role('user')->exists()) {
            User::factory()->count(50)->create()->each(function (User $user) {
                $user->syncRoles(['user']);
            });
        }

        // Give a few agents an upgrade so the plan limits are visible in the UI.
        $starter = Plan::where('slug', 'starter')->first();
        $professional = Plan::where('slug', 'professional')->first();

        if ($starter && $professional) {
            User::role('agent')->take(5)->get()->each(function (User $agent) use ($starter) {
                $agent->subscriptions()->firstOrCreate(
                    ['stripe_id' => 'sub_seed_'.$agent->id],
                    [
                        'type' => 'main',
                        'stripe_status' => 'active',
                        'stripe_price' => null,
                        'quantity' => 1,
                        'trial_ends_at' => null,
                        'ends_at' => null,
                        'plan_id' => $starter->id,
                    ]
                );
            });

            User::role('agent')->skip(5)->take(3)->get()->each(function (User $agent) use ($professional) {
                $agent->subscriptions()->firstOrCreate(
                    ['stripe_id' => 'sub_seed_prof_'.$agent->id],
                    [
                        'type' => 'main',
                        'stripe_status' => 'active',
                        'stripe_price' => null,
                        'quantity' => 1,
                        'trial_ends_at' => null,
                        'ends_at' => null,
                        'plan_id' => $professional->id,
                    ]
                );
            });
        }

        if (! User::where('is_suspended', true)->exists()) {
            User::factory()->suspended()->create();
        }
    }
}
