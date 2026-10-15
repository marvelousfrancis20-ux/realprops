<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MasterDataSeeder::class,
            PlanSeeder::class,
            UserSeeder::class,
            PropertySeeder::class,
            BlogSeeder::class,
            PageSeeder::class,
            SettingSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
