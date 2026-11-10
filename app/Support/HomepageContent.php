<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class HomepageContent
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'layout' => [
                'hero' => true,
                'steps' => true,
                'trust' => true,
                'categories' => true,
                'trending' => true,
                'reviews' => true,
                'journal' => true,
                'flash' => true,
            ],
            'top_bar' => [
                'left' => '100% Certified Authentic Skincare & Halal Health Essentials',
                'center' => 'Free Nationwide Delivery on Orders Over Rs. 2,599',
                'right' => 'Cash on Delivery Available',
                'phone_label' => 'Specialist Care',
                'phone' => '0300 1234567',
                'about_label' => 'About Us',
                'about_url' => '/our-story',
            ],
            'slides' => [
                [
                    'badge' => 'Dermatology approved',
                    'title' => 'Clinically Proven Skincare,',
                    'accent' => 'Crafted for Radiant Everyday Health',
                    'description' => 'Authentic international serums, gentle cleansers and pure daily supplements with cash on delivery.',
                    'checks' => "Gentle on Skin\nFast & Effective\nBarrier Protection\n100% Genuine",
                    'button_label' => 'Explore Collection',
                    'button_url' => '/collections',
                    'price_line' => 'From Rs. 1,500 with Free COD',
                    'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
                    'image_alt' => 'Hyaluronic serum bottle',
                ],
                [
                    'badge' => 'Bundle offers · 30% off',
                    'title' => 'Serums, sunscreens, and vitamins',
                    'accent' => 'in one daily routine',
                    'description' => 'Face serums, SPF 50+ sun care, and certified multivitamins with nationwide cash on delivery.',
                    'checks' => "SPF 50+ daily sun care\nCertified multivitamins\nBaby & mom care\nWhitening care",
                    'button_label' => 'Shop bundle offers',
                    'button_url' => '/exclusive-offers',
                    'price_line' => 'From Rs. 1,500 with Free COD',
                    'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
                    'image_alt' => 'Skincare bottles',
                ],
                [
                    'badge' => 'Halal health essentials',
                    'title' => 'Hair, glow, and family care',
                    'accent' => 'from trusted derma brands',
                    'description' => 'Shampoos, brightening care, and baby formulas picked for everyday Pakistan delivery.',
                    'checks' => "Hair fall care\nAcne and pore care\nDermatologist approved\nWhatsApp specialist support",
                    'button_label' => 'Browse brands',
                    'button_url' => '/collections',
                    'price_line' => 'From Rs. 1,500 with Free COD',
                    'image_url' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=800',
                    'image_alt' => 'Daily skincare set',
                ],
            ],
            'trust_row' => [
                ['icon' => 'badge-check', 'title' => '100% Genuine Imports', 'text' => 'Direct from verified suppliers'],
                ['icon' => 'shield-check', 'title' => 'Dermatologist Approved', 'text' => 'Clinical-grade formulas'],
                ['icon' => 'truck', 'title' => 'Nationwide Cash on Delivery', 'text' => 'Pay when the parcel arrives'],
                ['icon' => 'message-circle', 'title' => 'Specialist WhatsApp Support', 'text' => 'Ask before you order'],
            ],
            'shop_pills' => [
                ['label' => 'Face Serums', 'count' => '128 products', 'icon' => 'droplet', 'url' => '/collections?cat=skincare'],
                ['label' => 'Hair Shampoo', 'count' => '86 products', 'icon' => 'scissors', 'url' => '/collections?cat=hair-care'],
                ['label' => 'Sun Protection', 'count' => '64 products', 'icon' => 'sun', 'url' => '/collections?cat=skincare'],
                ['label' => 'Daily Vitamins', 'count' => '120 products', 'icon' => 'pill', 'url' => '/collections?cat=wellness-spa'],
                ['label' => 'Baby & Mom Care', 'count' => '54 products', 'icon' => 'baby', 'url' => '/collections'],
                ['label' => 'Whitening Care', 'count' => '96 products', 'icon' => 'sparkles', 'url' => '/collections?cat=skincare'],
            ],
            'showcase_goals' => [
                ['title' => 'Skin Brightening & Glow', 'text' => 'Glutathione, Vitamin C & Niacinamide', 'icon' => 'sun', 'url' => '/collections?cat=skincare'],
                ['title' => 'Hair Fall & Fast Regrowth', 'text' => 'Herbal Apple Shampoo & Biotin', 'icon' => 'scissors', 'url' => '/collections?cat=hair-care'],
                ['title' => 'Acne & Pore Tightening', 'text' => 'Salicylic Acid & Centella Barrier Repair', 'icon' => 'sparkles', 'url' => '/collections?cat=skincare'],
            ],
            'weekly_deals' => [
                'title' => 'Deals of the Week',
                'view_label' => 'View all',
                'view_url' => '/exclusive-offers',
                'banner' => [
                    'image_url' => 'https://images.unsplash.com/photo-1563822249366-3efb23b8e0c9?auto=format&fit=crop&q=80&w=900',
                    'kicker' => 'Featured',
                    'title' => 'Weekly spotlight picks',
                    'url' => '/products/blueberry-tea',
                ],
                'products' => [
                    ['name' => 'Blueberry Tea', 'price' => 'Rs. 2,000', 'old_price' => 'Rs. 2,500', 'save' => 'Save 20%', 'rating' => '4', 'reviews' => '2', 'image_url' => 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?auto=format&fit=crop&q=80&w=700', 'url' => '/products/blueberry-tea'],
                    ['name' => 'Forever Aloe Lips', 'price' => 'Rs. 3,500', 'old_price' => 'Rs. 4,000', 'save' => 'Save 13%', 'rating' => '4', 'reviews' => '2', 'image_url' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=700', 'url' => '/products/forever-aloe-lips'],
                    ['name' => 'Ayuvya Boo Beautiful Oil', 'price' => 'Rs. 2,500', 'old_price' => 'Rs. 3,000', 'save' => 'Save 17%', 'rating' => '5', 'reviews' => '3', 'image_url' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=700', 'url' => '/products/ayuvya-boo-beautiful-oil'],
                    ['name' => 'Vigini Bust Firming Massage Cream', 'price' => 'Rs. 2,500', 'old_price' => 'Rs. 3,000', 'save' => 'Save 17%', 'rating' => '4', 'reviews' => '4', 'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=700', 'url' => '/products/vigini-bust-firming-massage-cream'],
                    ['name' => 'Sidra Maximum Power Macun', 'price' => 'Rs. 7,500', 'old_price' => 'Rs. 9,500', 'save' => 'Save 21%', 'rating' => '5', 'reviews' => '5', 'image_url' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=700', 'url' => '/products/sidra-maximum-power-macun'],
                    ['name' => 'Slimming Capsule', 'price' => 'Rs. 4,499', 'old_price' => 'Rs. 5,000', 'save' => 'Save 10%', 'rating' => '4', 'reviews' => '7', 'image_url' => 'https://images.unsplash.com/photo-1584305574647-0cc949a2bb9f?auto=format&fit=crop&q=80&w=700', 'url' => '/products/slimming-capsule'],
                ],
            ],
            'featured_cards' => [
                ['name' => 'Glutathione Whitening Pills 200mg', 'price' => 'Rs. 2,700', 'old_price' => 'Rs. 3,500', 'rating' => '4.7', 'reviews' => '128', 'image_url' => 'https://images.unsplash.com/photo-1584305574647-0cc949a2bb9f?auto=format&fit=crop&q=80&w=800', 'url' => '/products/glutathione-whitening-pills-200mg'],
                ['name' => 'Apple Ammonia-Free Hair Shampoo', 'price' => 'Rs. 1,500', 'old_price' => 'Rs. 2,000', 'rating' => '4.5', 'reviews' => '86', 'image_url' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=800', 'url' => '/products/apple-ammonia-free-hair-shampoo'],
                ['name' => 'CeraVe Moisturising Cream 340g', 'price' => 'Rs. 3,850', 'old_price' => 'Rs. 4,500', 'rating' => '4.8', 'reviews' => '214', 'image_url' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&q=80&w=800', 'url' => '/products/cerave-moisturising-cream-340g'],
                ['name' => 'Centella Water-Fit Sun Serum 50ml', 'price' => 'Rs. 3,650', 'old_price' => 'Rs. 4,200', 'rating' => '4.6', 'reviews' => '97', 'image_url' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?auto=format&fit=crop&q=80&w=800', 'url' => '/products/centella-water-fit-sun-serum-50ml'],
                ['name' => 'The Ordinary Niacinamide Serum', 'price' => 'Rs. 2,450', 'old_price' => 'Rs. 2,900', 'rating' => '4.8', 'reviews' => '176', 'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800', 'url' => '/products/the-ordinary-niacinamide-serum'],
                ['name' => 'Cetaphil Gentle Skin Cleanser', 'price' => 'Rs. 1,850', 'old_price' => 'Rs. 2,200', 'rating' => '4.6', 'reviews' => '143', 'image_url' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=800', 'url' => '/products/cetaphil-gentle-skin-cleanser'],
                ['name' => 'Dr. Rashel Vitamin C Cream', 'price' => 'Rs. 1,250', 'old_price' => 'Rs. 1,650', 'rating' => '4.4', 'reviews' => '91', 'image_url' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&q=80&w=800', 'url' => '/products/dr-rashel-vitamin-c-cream'],
                ['name' => 'NutriFactor Biotin Hair Gummies', 'price' => 'Rs. 2,150', 'old_price' => 'Rs. 2,800', 'rating' => '4.5', 'reviews' => '64', 'image_url' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=800', 'url' => '/products/nutrifactor-biotin-hair-gummies'],
                ['name' => 'Hyaluronic Acid Hydration Serum', 'price' => 'Rs. 1,950', 'old_price' => 'Rs. 2,450', 'rating' => '4.7', 'reviews' => '152', 'image_url' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=800', 'url' => '/products/hyaluronic-acid-hydration-serum'],
                ['name' => 'SPF 50 Matte Sunscreen', 'price' => 'Rs. 1,750', 'old_price' => 'Rs. 2,200', 'rating' => '4.6', 'reviews' => '118', 'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800', 'url' => '/products/spf-50-matte-sunscreen'],
                ['name' => 'Rosehip Face Oil 30ml', 'price' => 'Rs. 2,250', 'old_price' => 'Rs. 2,900', 'rating' => '4.5', 'reviews' => '73', 'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800', 'url' => '/products/rosehip-face-oil-30ml'],
                ['name' => 'Salicylic Acid Acne Gel', 'price' => 'Rs. 1,450', 'old_price' => 'Rs. 1,850', 'rating' => '4.4', 'reviews' => '88', 'image_url' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?auto=format&fit=crop&q=80&w=800', 'url' => '/products/salicylic-acid-acne-gel'],
            ],
            'brand_cards' => [
                ['name' => 'CeraVe', 'count' => '32+ products', 'url' => '/collections'],
                ['name' => 'The Ordinary', 'count' => '42+ products', 'url' => '/collections'],
                ['name' => 'NutriFactor', 'count' => '18+ products', 'url' => '/collections'],
                ['name' => 'Dr. Rashel', 'count' => '67+ products', 'url' => '/collections'],
                ['name' => 'Apple Hair Color', 'count' => '11+ products', 'url' => '/collections?cat=hair-care'],
                ['name' => 'Yardlie Beauty', 'count' => '12+ products', 'url' => '/collections'],
                ['name' => 'Cetaphil', 'count' => '16+ products', 'url' => '/collections?cat=skincare'],
                ['name' => 'Lichen Herbal', 'count' => '22+ products', 'url' => '/collections'],
            ],
            'hero' => [
                'enabled' => true,
                'background_from' => null,
                'background_to' => null,
                'badge' => 'NEW QUICK BAZAR TECH RELEASE',
                'title_line1' => 'Lumina Pro 7-Spectrum',
                'title_accent' => 'LED Therapy Mask',
                'description' => 'Medical-grade dermal light therapy engine. Targets collagen revival, deep pigment balancing, and targeted cellular radiance.',
                'price_label' => 'Special Intro Price',
                'price_display' => '249',
                'compare_price_display' => '349',
                'button_label' => 'Reserve Order',
                'product_id' => null,
                'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=600',
                'image_alt' => 'Lumina Pro Mask',
            ],
            'promo_cards' => [
                [
                    'style' => 'light',
                    'badge' => 'Bestseller',
                    'title' => 'Botanical Youth Serum',
                    'description' => '100% Organic Hyaluronic Acid & Rose Stem Cells.',
                    'price_display' => '89',
                    'button_label' => 'Add To Bag',
                    'button_style' => 'text',
                    'product_id' => null,
                    'image_url' => '',
                ],
                [
                    'style' => 'gradient',
                    'badge' => 'Save 25%',
                    'title' => 'Sculpting Microcurrent Wand',
                    'description' => 'Tone & lift facial muscles with targeted EMS energy.',
                    'price_display' => '129',
                    'button_label' => 'Claim Offer',
                    'button_style' => 'solid',
                    'product_id' => null,
                    'image_url' => '',
                ],
            ],
            'category_intro' => [
                'badge' => 'QUICK BAZAR FLAGSHIP STORE • 100% ORIGINAL & VERIFIED PRODUCTS',
                'heading' => "Pakistan's Best Beauty Store: Shop Smart, Glow Gold Every Day",
                'description' => 'Browse thousands of authentic beauty essentials at the best online shopping store in Pakistan, from professional LED devices and organic botanical serums to luxury fragrances and hair care rituals, all in one trusted place.',
                'trust_badges' => [
                    ['icon' => 'shield-check', 'label' => 'Authorized Brand Stores'],
                    ['icon' => 'rotate-ccw', 'label' => 'Hassle-Free Returns'],
                    ['icon' => 'truck', 'label' => 'Cash on Delivery Available'],
                ],
            ],
            'category_tiles' => [
                [
                    'label' => 'Skincare',
                    'filter_value' => 'Skincare',
                    'collection_slug' => 'skincare',
                    'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Beauty Tech',
                    'filter_value' => 'Beauty Tech',
                    'collection_slug' => 'beauty-tech',
                    'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Hair Care',
                    'filter_value' => 'Haircare',
                    'collection_slug' => 'hair-care',
                    'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Fragrance',
                    'filter_value' => 'Fragrance',
                    'collection_slug' => 'fragrance',
                    'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Wellness & Spa',
                    'filter_value' => 'Wellness',
                    'collection_slug' => 'wellness',
                    'image_url' => 'https://images.unsplash.com/photo-1608248597359-f5383a886f49?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Organic Serums',
                    'filter_value' => 'Skincare',
                    'collection_slug' => 'skincare',
                    'image_url' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'View All Products',
                    'filter_value' => 'all',
                    'collection_slug' => '',
                    'image_url' => '',
                    'is_view_all' => true,
                ],
            ],
            'showcase' => [
                'badge' => 'Curated Departments',
                'heading' => 'Shop by Category Showcase',
                'description' => 'Explore our signature lines engineered for targeted elegance and results.',
            ],
            'flash_deals' => [
                'badge' => 'TIME-SENSITIVE LUXURY FLASH DEALS',
                'title' => 'Exclusive Flash Vault',
                'title_badge' => 'Up to 50% OFF',
                'description' => 'Lock in verified flagship skincare, advanced dermal tech, and artisanal fragrances at unmatched introductory prices before inventory depletion.',
                'spotlight_product_id' => null,
                'spotlight_badge' => 'Spotlight Deal',
            ],
            'aesthetics_gallery' => [
                'badge' => 'FLAGSHIP COLLECTIONS',
                'heading' => 'Explore Signature Aesthetics',
                'description' => 'Immerse yourself in our curated visual gallery highlighting premium craftsmanship and organic formulations.',
                'cards' => [
                    ['badge' => 'Pure Formulation', 'title' => 'Organic Botanicals', 'description' => 'Sustainably harvested plant extracts.', 'collection_slug' => 'skincare', 'image_url' => 'https://images.unsplash.com/photo-1608248597359-f5383a886f49?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Advanced Dermal', 'title' => 'Photon LED Tech', 'description' => 'Medical-grade light therapy at home.', 'collection_slug' => 'beauty-tech', 'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Signature Scent', 'title' => 'Artisanal Fragrances', 'description' => 'Enchanting notes for lasting elegance.', 'collection_slug' => 'fragrance', 'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Ionic Styling', 'title' => 'Luxury Hair Care', 'description' => 'Mirror shine and silky repair rituals.', 'collection_slug' => 'haircare', 'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=600'],
                ],
            ],
            'featured_promo' => [
                'eyebrow' => 'Dermal Innovation',
                'title' => 'Thermal Cryo Sculpting Engine',
                'description' => 'Dual-action cryo-cooling and deep thermal vibration for pore tightening, lymphatic drainage, and immediate jawline definition.',
                'product_id' => null,
                'visual_title' => 'Quick Bazar Thermal Cryo Wand',
                'visual_subtitle' => 'Ergonomic Rose-Gold Finish',
                'visual_badge' => 'Clinical Grade',
                'image_url' => '',
            ],
            'trending' => [
                'badge' => 'Curated Selection',
                'heading' => 'Trending Beauty & Wellness Catalog',
                'description' => 'High-performance formulations & devices designed to elevate your elegance',
            ],
            'endorsements' => [
                'badge' => 'Verified Stars & Icons',
                'heading' => 'Celebrity Endorsement',
                'description' => 'Watch live video testimonials, personal routines, and proven clinical results shared by our beloved stars and beauty experts.',
                'cards' => [
                    ['name' => 'Fahad Sheikh', 'badge' => 'Hair Growth', 'quote' => 'Alopecia & bald patches cured with hair growth bundle in 4 weeks.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Merium Pervaiz', 'badge' => 'Live Demo', 'quote' => 'Live usage & results of hair dye shampoo on her husband & father.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Humaima Malick', 'badge' => 'At-Home Facial', 'quote' => 'Look how our glitter mask & face polish gave complete at-home facial.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Dananeer Mobeen', 'badge' => 'Hair Care', 'quote' => 'Weekend pawri with champi of our bestseller hair growth bundle!', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Saboor Aly', 'badge' => 'Skin Glow', 'quote' => 'Saboor always prefers ubtan on expensive facials once a week application gives extreme glowing spotless skin.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Minal Khan', 'badge' => 'Pore Care', 'quote' => 'Minal Khan got super big pores which got completely vanished in 4 weeks by niacinamide & rose gel.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=500'],
                ],
            ],
        ];
    }

    /** @param  array<string, mixed>  $stored */
    public static function mergeWithDefaults(array $stored): array
    {
        $defaults = static::defaults();

        $merged = array_replace_recursive($defaults, $stored);

        if (! is_array($merged['promo_cards'] ?? null) || $merged['promo_cards'] === []) {
            $merged['promo_cards'] = $defaults['promo_cards'];
        }

        if (! is_array($merged['category_tiles'] ?? null) || $merged['category_tiles'] === []) {
            $merged['category_tiles'] = $defaults['category_tiles'];
        }

        if (! is_array($merged['category_intro']['trust_badges'] ?? null) || $merged['category_intro']['trust_badges'] === []) {
            $merged['category_intro']['trust_badges'] = $defaults['category_intro']['trust_badges'];
        }

        foreach (['slides', 'shop_pills', 'showcase_goals', 'featured_cards', 'brand_cards', 'trust_row', 'weekly_deals'] as $key) {
            if (! is_array($merged[$key] ?? null) || $merged[$key] === []) {
                $merged[$key] = $defaults[$key];
            }
        }

        foreach (['aesthetics_gallery.cards', 'endorsements.cards'] as $path) {
            if (! is_array(data_get($merged, $path)) || data_get($merged, $path) === []) {
                data_set($merged, $path, data_get($defaults, $path, []));
            }
        }

        return $merged;
    }

    /** @param  array<string, mixed>  $incoming
     * @param  array<string, mixed>  $previous */
    public static function preserveImageFields(array $incoming, array $previous): array
    {
        $heroImage = data_get($incoming, 'hero.image_url');
        if (blank($heroImage)) {
            data_set($incoming, 'hero.image_url', data_get($previous, 'hero.image_url'));
        }

        $tiles = data_get($incoming, 'category_tiles', []);
        if (is_array($tiles)) {
            foreach ($tiles as $index => $tile) {
                if (! is_array($tile)) {
                    continue;
                }
                if (! empty($tile['is_view_all'])) {
                    continue;
                }
                if (blank($tile['image_url'] ?? null)) {
                    $prev = data_get($previous, "category_tiles.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "category_tiles.{$index}.image_url", $prev);
                    }
                }
            }
        }

        $promos = data_get($incoming, 'promo_cards', []);
        if (is_array($promos)) {
            foreach ($promos as $index => $card) {
                if (! is_array($card)) {
                    continue;
                }
                if (blank($card['image_url'] ?? null)) {
                    $prev = data_get($previous, "promo_cards.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "promo_cards.{$index}.image_url", $prev);
                    }
                }
            }
        }

        foreach (['aesthetics_gallery.cards', 'endorsements.cards'] as $path) {
            $items = data_get($incoming, $path, []);
            if (! is_array($items)) {
                continue;
            }
            foreach ($items as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (blank($item['image_url'] ?? null)) {
                    $prev = data_get($previous, "{$path}.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "{$path}.{$index}.image_url", $prev);
                    }
                }
            }
        }

        $featuredImage = data_get($incoming, 'featured_promo.image_url');
        if (blank($featuredImage)) {
            data_set($incoming, 'featured_promo.image_url', data_get($previous, 'featured_promo.image_url'));
        }

        return $incoming;
    }

    public static function resolveProduct(mixed $productId, bool $flashFallback = false): ?Product
    {
        $id = (int) $productId;
        if ($id > 0) {
            return Product::query()->find($id);
        }

        if ($flashFallback) {
            return Product::query()
                ->where('is_flash', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        }

        return null;
    }

    /** @param  array<string, mixed>  $tile */
    public static function resolveTileCategorySlug(array $tile): ?string
    {
        $slug = trim((string) ($tile['collection_slug'] ?? ''));
        if ($slug !== '') {
            return $slug;
        }

        $needles = array_values(array_unique(array_filter([
            $tile['label'] ?? null,
            $tile['filter_value'] ?? null,
        ], fn ($value) => is_string($value) && $value !== '' && $value !== 'all')));

        foreach ($needles as $needle) {
            $matched = static::matchCategorySlug($needle);
            if ($matched) {
                return $matched;
            }
        }

        return null;
    }

    public static function matchCategorySlug(string $needle): ?string
    {
        $needle = trim($needle);
        if ($needle === '') {
            return null;
        }

        $needleSlug = Str::slug($needle);
        $needleCompact = strtolower(preg_replace('/[^a-z0-9]+/i', '', $needle) ?? '');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $category) {
            $name = (string) $category->name;
            $navLabel = (string) ($category->nav_label ?? '');
            $nameSlug = Str::slug($name);
            $nameCompact = strtolower(preg_replace('/[^a-z0-9]+/i', '', $name) ?? '');

            if ($category->slug === $needleSlug || $nameSlug === $needleSlug) {
                return $category->slug;
            }

            if ($needleCompact !== '' && ($nameCompact === $needleCompact || str_contains($nameCompact, $needleCompact) || str_contains($needleCompact, $nameCompact))) {
                return $category->slug;
            }

            if (stripos($name, $needle) !== false || ($navLabel !== '' && stripos($navLabel, $needle) !== false)) {
                return $category->slug;
            }
        }

        return null;
    }

    /** @param  array<string, mixed>  $tile */
    public static function tileCollectionUrl(array $tile): string
    {
        if (! empty($tile['is_view_all']) || ($tile['filter_value'] ?? '') === 'all') {
            return route('collections');
        }

        $slug = static::resolveTileCategorySlug($tile);
        if ($slug) {
            return route('collections', ['cat' => $slug]);
        }

        return route('collections');
    }

    public static function imageUrl(?string $path, string $fallbackAlt = ''): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return 'https://placehold.co/400x400/5E1F70/FFFFFF?text='.urlencode($fallbackAlt ?: 'Image');
        }

        return SiteSettings::assetUrl($path, 'images/beautagelogosquare2.png');
    }
}
