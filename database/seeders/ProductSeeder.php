<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'slug' => 'lumina-pro-led-mask',
                'name' => 'Lumina Pro 7-Spectrum LED Mask',
                'category' => 'Beauty Tech',
                'price' => 249.00,
                'old_price' => 349.00,
                'rating' => 4.9,
                'reviews' => 128,
                'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=600',
                'hover_image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=600',
                'badge' => 'Flagship Device',
                'is_flash' => true,
                'discount' => 'Save 30%',
                'claimed_percent' => 84,
                'claimed_text' => '4 units left',
                'description' => 'Medical-grade photon light therapy for targeted cellular rejuvenation and acne clearance.',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800',
                ],
                'sort_order' => 1,
            ],
            [
                'slug' => 'botanical-youth-renewal-serum',
                'name' => 'Botanical Youth Renewal Serum',
                'category' => 'Skincare',
                'price' => 89.00,
                'old_price' => 115.00,
                'rating' => 4.8,
                'reviews' => 94,
                'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=600',
                'hover_image' => 'https://images.unsplash.com/photo-1608248597359-f5383a886f49?auto=format&fit=crop&q=80&w=600',
                'badge' => 'Bestseller',
                'is_flash' => true,
                'discount' => 'Save 22%',
                'claimed_percent' => 71,
                'claimed_text' => '9 units left',
                'description' => '100% organic hyaluronic acid combined with Damascus rose stem cells for ultimate skin hydration.',
                'sort_order' => 2,
            ],
        ];

        foreach ($products as $productData) {
            $categoryName = match ($productData['category']) {
                'Haircare' => 'Hair Care',
                default => $productData['category'],
            };

            $categoryId = Category::query()->where('name', $categoryName)->value('id');

            Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, ['category_id' => $categoryId]),
            );
        }
    }
}
