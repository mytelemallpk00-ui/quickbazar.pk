<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Skincare', 'icon' => 'droplet'],
            ['name' => 'Beauty Tech', 'icon' => 'zap'],
            ['name' => 'Hair Care', 'icon' => 'scissors'],
            ['name' => 'Fragrance', 'icon' => 'flower-2'],
            ['name' => 'Wellness & Spa', 'icon' => 'sparkles'],
        ];

        foreach ($categories as $index => $category) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'show_in_nav' => true,
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
