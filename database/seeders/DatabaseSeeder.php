<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AdminUserSeeder::class,
            TemplateCategoriesSeeder::class,
            AnimationEffectsSeeder::class,
            StickyPackagesSeeder::class, // Добавляем новый сидер для стиков
            SubscriptionPlanSeeder::class,
        ]);
    }
}
