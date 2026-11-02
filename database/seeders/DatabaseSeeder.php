<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingsSeeder::class,
            CategorySeeder::class,
            AdminUserSeeder::class,
            CmsPageSeeder::class,
            CareerOpeningSeeder::class,
            ProductSeeder::class,
            BlogPostSeeder::class,
        ]);
    }
}
