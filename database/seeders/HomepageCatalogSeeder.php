<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class HomepageCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id', 'slug');

        foreach ($this->products() as $index => $row) {
            $categoryId = $categoryIds[$row['category_slug']] ?? null;
            $image = $row['image'];

            $product = Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'category_id' => $categoryId,
                    'name' => $row['name'],
                    'category' => $row['category'],
                    'price' => $row['price'],
                    'old_price' => $row['old_price'],
                    'rating' => $row['rating'],
                    'reviews' => $row['reviews'],
                    'image' => $image,
                    'hover_image' => $image,
                    'badge' => $row['badge'],
                    'is_flash' => $row['is_flash'],
                    'discount' => $row['discount'],
                    'claimed_percent' => $row['claimed_percent'],
                    'claimed_text' => 'In stock',
                    'description' => $row['summary'],
                    'short_description' => $row['summary'],
                    'in_stock' => true,
                    'stock_quantity' => 40,
                    'gallery_images' => [$image],
                    'sort_order' => 30 + $index,
                    'detail_heading' => $row['name'],
                    'detail_body' => $row['summary'].' The price on this page is the catalog price in PKR, and it is the same price added to the bag.',
                    'detail_highlights' => [
                        ['title' => 'PKR price', 'description' => 'What you see here is what checkout charges.'],
                        ['title' => 'Nationwide delivery', 'description' => 'Cash on delivery is available when checkout offers it for your city.'],
                    ],
                    'specs_heading' => 'Product details',
                    'specs_rows' => [
                        ['label' => 'Category', 'value' => $row['category']],
                        ['label' => 'Price', 'value' => 'Rs. '.number_format($row['price'])],
                        ['label' => 'Availability', 'value' => 'In stock'],
                    ],
                    'routine_heading' => 'How to use',
                    'routine_body' => 'Follow the pack directions. Patch test first, then use it in your usual morning or evening routine.',
                    'promo_label' => $row['discount'],
                    'units_left' => 12,
                ],
            );

            if ($product->productReviews()->exists()) {
                $product->updateQuietly([
                    'rating' => $row['rating'],
                    'reviews' => $row['reviews'],
                ]);

                continue;
            }

            ProductReview::withoutEvents(function () use ($product): void {
                ProductReview::query()->create([
                    'product_id' => $product->id,
                    'author_name' => 'Ayesha Khan',
                    'rating' => 5,
                    'title' => 'Easy to order',
                    'body' => 'Arrived packed well and matched the price shown on the product page.',
                    'is_verified_buyer' => true,
                    'is_published' => true,
                ]);
                ProductReview::query()->create([
                    'product_id' => $product->id,
                    'author_name' => 'Hina Raza',
                    'rating' => 4,
                    'title' => 'Would buy again',
                    'body' => 'Clear listing, cash on delivery, and the product was as described.',
                    'is_verified_buyer' => true,
                    'is_published' => true,
                ]);
            });

            $product->updateQuietly([
                'rating' => $row['rating'],
                'reviews' => $row['reviews'],
            ]);
        }
    }

    /** @return list<array<string, mixed>> */
    private function products(): array
    {
        return [
            $this->row('glutathione-whitening-pills-200mg', 'Glutathione Whitening Pills 200mg', 'wellness-spa', 'Wellness & Spa', 2700, 3500, 4.7, 128, 'https://images.unsplash.com/photo-1584305574647-0cc949a2bb9f?auto=format&fit=crop&q=80&w=800', 'Daily whitening support from the wellness shelf.', false),
            $this->row('apple-ammonia-free-hair-shampoo', 'Apple Ammonia-Free Hair Shampoo', 'hair-care', 'Hair Care', 1500, 2000, 4.5, 86, 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=800', 'A gentle apple shampoo for regular hair washing.', false),
            $this->row('cerave-moisturising-cream-340g', 'CeraVe Moisturising Cream 340g', 'skincare', 'Skincare', 3850, 4500, 4.8, 214, 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&q=80&w=800', 'A rich moisturising cream for dry skin.', false),
            $this->row('centella-water-fit-sun-serum-50ml', 'Centella Water-Fit Sun Serum 50ml', 'skincare', 'Skincare', 3650, 4200, 4.6, 97, 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&q=80&w=800', 'A light sun serum for daytime wear.', false),
            $this->row('the-ordinary-niacinamide-serum', 'The Ordinary Niacinamide Serum', 'skincare', 'Skincare', 2450, 2900, 4.8, 176, 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800', 'A niacinamide serum for uneven-looking skin.', false),
            $this->row('cetaphil-gentle-skin-cleanser', 'Cetaphil Gentle Skin Cleanser', 'skincare', 'Skincare', 1850, 2200, 4.6, 143, 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=800', 'A mild cleanser for everyday face washing.', false),
            $this->row('dr-rashel-vitamin-c-cream', 'Dr. Rashel Vitamin C Cream', 'skincare', 'Skincare', 1250, 1650, 4.4, 91, 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&q=80&w=800', 'A vitamin C cream for a brighter-looking complexion.', false),
            $this->row('nutrifactor-biotin-hair-gummies', 'NutriFactor Biotin Hair Gummies', 'hair-care', 'Hair Care', 2150, 2800, 4.5, 64, 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=800', 'Biotin gummies for a simple hair-care routine.', false),
            $this->row('hyaluronic-acid-hydration-serum', 'Hyaluronic Acid Hydration Serum', 'skincare', 'Skincare', 1950, 2450, 4.7, 152, 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=800', 'A hydrating serum to layer under moisturiser.', false),
            $this->row('spf-50-matte-sunscreen', 'SPF 50 Matte Sunscreen', 'skincare', 'Skincare', 1750, 2200, 4.6, 118, 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800', 'A matte SPF 50 sunscreen for daily sun protection.', false),
            $this->row('rosehip-face-oil-30ml', 'Rosehip Face Oil 30ml', 'skincare', 'Skincare', 2250, 2900, 4.5, 73, 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800', 'A lightweight rosehip oil for the last step of a routine.', false),
            $this->row('salicylic-acid-acne-gel', 'Salicylic Acid Acne Gel', 'skincare', 'Skincare', 1450, 1850, 4.4, 88, 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&q=80&w=800', 'A targeted gel for blemish-prone skin.', false),
            $this->row('blueberry-tea', 'Blueberry Tea', 'wellness-spa', 'Wellness & Spa', 2000, 2500, 4.0, 2, 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?auto=format&fit=crop&q=80&w=700', 'A blueberry tea from this week’s deal shelf.', true),
            $this->row('forever-aloe-lips', 'Forever Aloe Lips', 'skincare', 'Skincare', 3500, 4000, 4.0, 2, 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=700', 'A lip care balm from this week’s deals.', true),
            $this->row('ayuvya-boo-beautiful-oil', 'Ayuvya Boo Beautiful Oil', 'skincare', 'Skincare', 2500, 3000, 5.0, 3, 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=700', 'A body oil from this week’s deals.', true),
            $this->row('vigini-bust-firming-massage-cream', 'Vigini Bust Firming Massage Cream', 'skincare', 'Skincare', 2500, 3000, 4.0, 4, 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=700', 'A massage cream from this week’s deals.', true),
            $this->row('sidra-maximum-power-macun', 'Sidra Maximum Power Macun', 'wellness-spa', 'Wellness & Spa', 7500, 9500, 5.0, 5, 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=700', 'A wellness macun from this week’s deals.', true),
            $this->row('slimming-capsule', 'Slimming Capsule', 'wellness-spa', 'Wellness & Spa', 4499, 5000, 4.0, 7, 'https://images.unsplash.com/photo-1584305574647-0cc949a2bb9f?auto=format&fit=crop&q=80&w=700', 'A wellness capsule from this week’s deals.', true),
        ];
    }

    /** @return array<string, mixed> */
    private function row(
        string $slug,
        string $name,
        string $categorySlug,
        string $category,
        float $price,
        float $oldPrice,
        float $rating,
        int $reviews,
        string $image,
        string $summary,
        bool $isFlash,
    ): array {
        $save = $oldPrice > $price ? (int) round((($oldPrice - $price) / $oldPrice) * 100) : 0;

        return [
            'slug' => $slug,
            'name' => $name,
            'category_slug' => $categorySlug,
            'category' => $category,
            'price' => $price,
            'old_price' => $oldPrice,
            'rating' => $rating,
            'reviews' => $reviews,
            'image' => $image,
            'summary' => $summary,
            'badge' => $isFlash ? 'Weekly deal' : 'Bestseller',
            'is_flash' => $isFlash,
            'discount' => $save > 0 ? 'Save '.$save.'%' : null,
            'claimed_percent' => min(90, 40 + ($reviews % 40)),
        ];
    }
}
