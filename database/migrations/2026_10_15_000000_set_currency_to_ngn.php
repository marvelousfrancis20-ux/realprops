<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Switch every existing money record over to Naira (₦).
     */
    public function up(): void
    {
        $planPrices = [
            'free' => 0,
            'starter' => 2500,
            'professional' => 7500,
            'enterprise' => 15000,
        ];

        foreach ($planPrices as $slug => $price) {
            DB::table('plans')
                ->where('slug', $slug)
                ->where('currency', '!=', 'NGN')
                ->update(['currency' => 'NGN', 'price' => $price]);
        }

        DB::table('plans')->where('currency', '!=', 'NGN')->update(['currency' => 'NGN']);
        DB::table('properties')->where('currency', '!=', 'NGN')->update(['currency' => 'NGN']);

        DB::table('settings')->where('key', 'currency')->update(['value' => '₦']);
        DB::table('settings')->where('key', 'featured_listing_price')->where('value', '19.99')->update(['value' => '2500']);
    }

    public function down(): void
    {
        //
    }
};
