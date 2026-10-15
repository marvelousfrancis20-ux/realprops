<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Get started with up to 3 live listings.',
                'price' => 0,
                'currency' => 'NGN',
                'billing_interval' => 'month',
                'listing_limit' => 3,
                'allows_featured' => false,
                'features' => ['3 live listings', 'Basic analytics', 'Email support'],
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        Plan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'description' => 'For growing agents who need more exposure.',
                'price' => 2500,
                'currency' => 'NGN',
                'billing_interval' => 'month',
                'listing_limit' => 15,
                'allows_featured' => false,
                'features' => ['15 live listings', 'Standard analytics', 'Priority email support'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Plan::firstOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional',
                'description' => 'Featured listings and full analytics for pros.',
                'price' => 7500,
                'currency' => 'NGN',
                'billing_interval' => 'month',
                'listing_limit' => 50,
                'allows_featured' => true,
                'features' => ['50 live listings', 'Featured listings', 'Full analytics', 'Phone support'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Plan::firstOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'description' => 'Unlimited listings for agencies and teams.',
                'price' => 15000,
                'currency' => 'NGN',
                'billing_interval' => 'month',
                'listing_limit' => 0,
                'allows_featured' => true,
                'features' => ['Unlimited listings', 'Featured listings', 'Team seats', 'Dedicated support'],
                'is_active' => true,
                'sort_order' => 3,
            ]
        );
    }
}
