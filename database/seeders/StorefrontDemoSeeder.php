<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorefrontDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->brandSettings();
        $this->categorySeo();

        foreach ($this->products() as $index => $row) {
            $this->upsertProduct($row, $index + 1);
        }

        foreach ($this->posts() as $row) {
            $this->upsertPost($row);
        }
    }

    private function brandSettings(): void
    {
        SiteSettings::set('site_name', 'Quick Bazar', 'site');
        SiteSettings::set('site_tagline', 'Verified products, delivered across Pakistan', 'site');
        SiteSettings::set('default_meta_title', 'Quick Bazar — Shop verified products across Pakistan', 'seo');
        SiteSettings::set('default_meta_description', 'Shop original products on Quick Bazar with PKR prices, cash on delivery, nationwide shipping, and verified customer reviews.', 'seo');
        SiteSettings::set('default_robots', 'index,follow', 'seo');

        $footer = SiteSettings::get('footer_settings', []);
        if (is_array($footer) && $footer !== []) {
            array_walk_recursive($footer, function (&$value): void {
                if (! is_string($value)) {
                    return;
                }
                $value = str_replace(
                    ['Quick Bazar PVT Ltd.', 'Quick Bazar', 'QUICK BAZAR'],
                    ['Quick Bazar', 'Quick Bazar', 'QUICK BAZAR'],
                    $value,
                );
            });
            SiteSettings::set('footer_settings', $footer, 'footer');
        }
    }

    private function categorySeo(): void
    {
        $copy = [
            'Skincare' => 'Shop serums, creams, and daily skincare on Quick Bazar with cash on delivery across Pakistan.',
            'Beauty Tech' => 'Shop LED masks and at-home beauty devices on Quick Bazar, with PKR prices and nationwide delivery.',
            'Hair Care' => 'Shop shampoos, oils, and scalp care on Quick Bazar. Original products, clear prices, cash on delivery.',
            'Fragrance' => 'Shop everyday and evening fragrances on Quick Bazar. Compare prices and read verified reviews.',
            'Wellness & Spa' => 'Shop spa tools and wellness essentials on Quick Bazar, delivered across Pakistan.',
        ];

        foreach ($copy as $name => $description) {
            Category::query()->where('name', $name)->update([
                'description' => $description,
                'meta_title' => $name.' — Quick Bazar',
                'meta_description' => $description,
                'seo_focus_keyword' => Str::lower($name).' Pakistan',
                'robots' => 'index,follow',
            ]);
        }
    }

    private function upsertProduct(array $row, int $sort): void
    {
        $categoryId = Category::query()->where('name', $row['category'])->value('id');
        $description = $row['description'];
        $meta = Str::limit($description, 155, '');

        $product = Product::query()->updateOrCreate(
            ['slug' => $row['slug']],
            [
                'category_id' => $categoryId,
                'name' => $row['name'],
                'category' => $row['category'],
                'price' => $row['price'],
                'old_price' => $row['old_price'],
                'image' => $row['image'],
                'hover_image' => $row['hover'],
                'badge' => $row['badge'],
                'is_flash' => $row['flash'],
                'discount' => $row['discount'],
                'claimed_percent' => $row['claimed'],
                'claimed_text' => $row['units'].' units left',
                'description' => $description,
                'short_description' => $row['short'],
                'in_stock' => true,
                'stock_quantity' => 18 + ($sort % 12),
                'gallery_images' => [$row['image'], $row['hover']],
                'sort_order' => $sort,
                'detail_heading' => $row['name'],
                'detail_body' => $description,
                'detail_highlights' => $row['highlights'],
                'specs_heading' => 'Specifications',
                'specs_rows' => $row['specs'],
                'routine_heading' => 'How to use',
                'routine_body' => $row['routine'],
                'promo_label' => $row['discount'],
                'units_left' => $row['units'],
                'meta_title' => $row['name'].' | Quick Bazar',
                'meta_description' => $meta,
                'meta_keywords' => $row['keyword'].', Quick Bazar, Pakistan, cash on delivery',
                'og_title' => $row['name'].' | Quick Bazar',
                'og_description' => $meta,
                'og_image' => $row['image'],
                'seo_focus_keyword' => $row['keyword'],
                'robots' => 'index,follow',
                'is_seo_optimized' => true,
            ],
        );

        foreach ($row['reviews'] as $offset => $review) {
            ProductReview::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'author_name' => $review['name'],
                ],
                [
                    'rating' => $review['rating'],
                    'title' => $review['title'],
                    'body' => $review['body'],
                    'is_verified_buyer' => true,
                    'is_published' => true,
                    'created_at' => now()->subDays(2 + ($offset * 3) + $sort),
                    'updated_at' => now()->subDays(1 + $offset),
                ],
            );
        }

        $product->refreshReviewAggregates();
    }

    private function upsertPost(array $row): void
    {
        $meta = Str::limit($row['snippet'], 155, '');

        BlogPost::query()->updateOrCreate(
            ['slug' => $row['slug']],
            [
                'title' => $row['title'],
                'category' => $row['category'],
                'read_time' => $row['read'],
                'published_at' => $row['date'],
                'image' => $row['image'],
                'author' => $row['author'],
                'author_role' => $row['role'],
                'author_avatar' => $row['avatar'],
                'likes' => $row['likes'],
                'snippet' => $row['snippet'],
                'content' => $row['content'],
                'is_featured' => $row['featured'],
                'meta_title' => $row['title'].' | Quick Bazar Journal',
                'meta_description' => $meta,
                'meta_keywords' => $row['category'].', Quick Bazar, Pakistan',
                'og_title' => $row['title'],
                'og_description' => $meta,
                'og_image' => $row['image'],
                'seo_focus_keyword' => $row['keyword'],
                'robots' => 'index,follow',
            ],
        );
    }

    /** @return list<array<string, mixed>> */
    private function products(): array
    {
        $img = fn (string $id): string => 'https://images.unsplash.com/'.$id.'?auto=format&fit=crop&q=80&w=800';

        return [
            $this->item('lumina-pro-led-mask', 'Lumina Pro 7-Spectrum LED Mask', 'Beauty Tech', 249, 349, 'Flagship Device', true, 'Save 30%', 84, 4, $img('photo-1598440947619-2c35fc9aa908'), $img('photo-1522337360788-8b13dee7a37e'), 'LED face mask Pakistan', 'Medical-grade photon light therapy for targeted cellular rejuvenation and acne clearance.', 'Wear 10 minutes, 3 times a week, on clean dry skin.', [
                ['title' => 'Seven light modes', 'description' => 'Red, blue, and near-infrared modes for tone, breakouts, and recovery.'],
                ['title' => 'At-home sessions', 'description' => 'Rechargeable mask with a 10-minute guided cycle.'],
            ], [
                ['label' => 'Session length', 'value' => '10 minutes'],
                ['label' => 'Power', 'value' => 'USB-C rechargeable'],
            ]),
            $this->item('botanical-youth-renewal-serum', 'Botanical Youth Renewal Serum', 'Skincare', 89, 115, 'Bestseller', true, 'Save 22%', 71, 9, $img('photo-1620916566398-39f1143ab7be'), $img('photo-1608248597359-f5383a886f49'), 'hyaluronic serum Pakistan', 'Hyaluronic acid serum for all-day hydration and a smoother look.', 'Apply 3 drops morning and night after cleansing.', [
                ['title' => 'Lightweight texture', 'description' => 'Sinks in under moisturizer and makeup.'],
                ['title' => 'Daily hydration', 'description' => 'Helps skin look plump through a dry day.'],
            ], [
                ['label' => 'Size', 'value' => '30 ml'],
                ['label' => 'Skin type', 'value' => 'All, including dry'],
            ]),
            $this->item('rosewater-gel-cream', 'Rosewater Gel Cream', 'Skincare', 1450, 1890, 'New', false, 'Save 23%', 46, 14, $img('photo-1556228720-195a672e8a03'), $img('photo-1612817288484-6f916006741a'), 'gel cream Pakistan', 'A light gel cream with rosewater for oily and combination skin.', 'Use as the last step of your morning routine.', [
                ['title' => 'Oil-free finish', 'description' => 'Hydrates without a heavy film.'],
                ['title' => 'Cooling feel', 'description' => 'Useful after cleansing or a sunny commute.'],
            ], [
                ['label' => 'Size', 'value' => '50 ml'],
                ['label' => 'Texture', 'value' => 'Gel cream'],
            ]),
            $this->item('vitamin-c-bright-drops', 'Vitamin C Bright Drops', 'Skincare', 1890, 2400, 'Brightening', false, 'Save 21%', 58, 11, $img('photo-1571781926291-c477ebfd024b'), $img('photo-1556228578-0d85b1a4d571'), 'vitamin C serum Pakistan', 'A morning vitamin C serum for dull skin and uneven tone.', 'Use 4 drops each morning, then apply sunscreen.', [
                ['title' => 'Morning step', 'description' => 'Designed to sit under sunscreen.'],
                ['title' => 'Even tone', 'description' => 'A simple brightening step for daily use.'],
            ], [
                ['label' => 'Size', 'value' => '20 ml'],
                ['label' => 'Use', 'value' => 'Morning'],
            ]),
            $this->item('sonic-cleansing-brush', 'Sonic Cleansing Brush', 'Beauty Tech', 3290, 4190, 'Device', true, 'Save 21%', 63, 7, $img('photo-1522335789203-aabd1fc54bc9'), $img('photo-1487412947147-5cebf100ffc2'), 'sonic cleansing brush', 'A two-speed sonic brush for a cleaner first step at the sink.', 'Wet the brush, use a pea of cleanser, and glide for 60 seconds.', [
                ['title' => 'Two speeds', 'description' => 'Gentle daily mode and a deeper weekly mode.'],
                ['title' => 'Travel case', 'description' => 'Charges by USB and packs flat.'],
            ], [
                ['label' => 'Charge', 'value' => 'USB'],
                ['label' => 'Modes', 'value' => '2'],
            ]),
            $this->item('ice-roller-face-tool', 'Cooling Ice Roller', 'Beauty Tech', 990, 1490, 'Spa tool', false, 'Save 34%', 39, 16, $img('photo-1515377905703-c4788e51af15'), $img('photo-1544161515-4ab6ce6db874'), 'ice roller Pakistan', 'A stainless roller to cool puffiness before makeup or after a long day.', 'Chill for 10 minutes, then roll upward for 2 minutes.', [
                ['title' => 'Morning depuff', 'description' => 'A quick step before sunscreen.'],
                ['title' => 'Easy clean', 'description' => 'Wipe the metal head after each use.'],
            ], [
                ['label' => 'Material', 'value' => 'Stainless steel'],
                ['label' => 'Care', 'value' => 'Wipe clean'],
            ]),
            $this->item('argan-hair-oil', 'Argan Shine Hair Oil', 'Hair Care', 1250, 1600, 'Hair', false, 'Save 22%', 52, 12, $img('photo-1522337360788-8b13dee7a37e'), $img('photo-1519699047748-de8e457a634e'), 'argan hair oil Pakistan', 'A few drops of argan oil for dry ends and frizz on wash day.', 'Warm 2 drops in your palms and smooth over mid-lengths and ends.', [
                ['title' => 'Ends only', 'description' => 'Keeps roots from looking oily.'],
                ['title' => 'Light scent', 'description' => 'A soft finish that does not overpower perfume.'],
            ], [
                ['label' => 'Size', 'value' => '50 ml'],
                ['label' => 'Hair type', 'value' => 'Dry or frizz-prone'],
            ]),
            $this->item('scalp-refresh-shampoo', 'Scalp Refresh Shampoo', 'Hair Care', 980, 1290, 'Daily', false, 'Save 24%', 44, 18, $img('photo-1556228578-8c89e6adf883'), $img('photo-1470259078422-826894b933aa'), 'scalp shampoo Pakistan', 'A gentle daily shampoo that lifts oil without stripping length.', 'Massage into the scalp for one minute, then rinse.', [
                ['title' => 'Scalp first', 'description' => 'Focus the lather at the roots.'],
                ['title' => 'Daily wash', 'description' => 'Mild enough for frequent washing.'],
            ], [
                ['label' => 'Size', 'value' => '250 ml'],
                ['label' => 'Use', 'value' => 'Daily or every other day'],
            ]),
            $this->item('repair-hair-mask', 'Weekly Repair Hair Mask', 'Hair Care', 1650, 2100, 'Treatment', false, 'Save 21%', 33, 10, $img('photo-1519699047748-de8e457a634e'), $img('photo-1522337360788-8b13dee7a37e'), 'hair mask Pakistan', 'A once-a-week mask for hair that feels rough after heat styling.', 'Leave on for 8 minutes after shampoo, then rinse cool.', [
                ['title' => 'Weekly ritual', 'description' => 'One mask replaces a heavy daily conditioner.'],
                ['title' => 'Heat styling', 'description' => 'Helps hair feel smoother before a blow-dry.'],
            ], [
                ['label' => 'Size', 'value' => '200 ml'],
                ['label' => 'Frequency', 'value' => 'Once a week'],
            ]),
            $this->item('citrus-day-eau', 'Citrus Day Eau', 'Fragrance', 2890, 3490, 'Day scent', false, 'Save 17%', 41, 8, $img('photo-1541643600914-78b084683601'), $img('photo-1594035910387-fea47794261f'), 'citrus perfume Pakistan', 'A light citrus eau for office days and warm afternoons.', 'Spray once on pulse points. It stays close to the skin.', [
                ['title' => 'Office friendly', 'description' => 'A fresh scent that does not fill a room.'],
                ['title' => 'Warm weather', 'description' => 'Citrus and clean musk.'],
            ], [
                ['label' => 'Size', 'value' => '50 ml'],
                ['label' => 'Style', 'value' => 'Eau de toilette'],
            ]),
            $this->item('oud-evening-attar', 'Oud Evening Attar', 'Fragrance', 4590, 5600, 'Evening', true, 'Save 18%', 67, 5, $img('photo-1594035910387-fea47794261f'), $img('photo-1541643600914-78b084683601'), 'oud attar Pakistan', 'A small-bottle oud attar for evenings and gatherings.', 'Dab a little on wrists. A drop is enough.', [
                ['title' => 'Concentrated', 'description' => 'One dab lasts through dinner.'],
                ['title' => 'Travel size', 'description' => 'Fits in a small pouch.'],
            ], [
                ['label' => 'Size', 'value' => '12 ml'],
                ['label' => 'Style', 'value' => 'Attar'],
            ]),
            $this->item('linen-mist', 'Clean Linen Room & Fabric Mist', 'Fragrance', 890, 1190, 'Home', false, 'Save 25%', 28, 20, $img('photo-1615634260167-c8cdede054de'), $img('photo-1592945403244-b3fbafd7f539'), 'linen mist Pakistan', 'A light linen mist for clothes, curtains, and a fresh room.', 'Spray from an arm’s length onto fabric.', [
                ['title' => 'Fabric safe', 'description' => 'Made for clothes and soft furnishings.'],
                ['title' => 'Light scent', 'description' => 'Clean cotton, not a heavy perfume.'],
            ], [
                ['label' => 'Size', 'value' => '100 ml'],
                ['label' => 'Use', 'value' => 'Fabric and room'],
            ]),
            $this->item('jade-gua-sha', 'Jade Gua Sha', 'Wellness & Spa', 1190, 1590, 'Tool', false, 'Save 25%', 36, 15, $img('photo-1544161515-4ab6ce6db874'), $img('photo-1540555700478-4be289fbecef'), 'gua sha Pakistan', 'A jade gua sha for a five-minute face massage with your serum.', 'Glide upward with light pressure over a slippery serum.', [
                ['title' => 'Five minutes', 'description' => 'A short evening massage, not a clinic treatment.'],
                ['title' => 'Stone tool', 'description' => 'Cool stone that is easy to rinse.'],
            ], [
                ['label' => 'Material', 'value' => 'Jade stone'],
                ['label' => 'Care', 'value' => 'Rinse and dry'],
            ]),
            $this->item('lavender-bath-soak', 'Lavender Bath Soak', 'Wellness & Spa', 1350, 1750, 'Evening', false, 'Save 23%', 31, 13, $img('photo-1540555700478-4be289fbecef'), $img('photo-1515377905703-c4788e51af15'), 'bath soak Pakistan', 'A lavender soak for a short bath at the end of the week.', 'Stir two scoops into warm water.', [
                ['title' => 'Evening wind-down', 'description' => 'Lavender scent for a quiet bath.'],
                ['title' => 'Simple scoop', 'description' => 'No measuring tools required.'],
            ], [
                ['label' => 'Size', 'value' => '300 g'],
                ['label' => 'Scent', 'value' => 'Lavender'],
            ]),
            $this->item('silk-sleep-mask', 'Silk Sleep Mask', 'Wellness & Spa', 1490, 1990, 'Sleep', false, 'Save 25%', 48, 9, $img('photo-1512496015851-a90fb38ba796'), $img('photo-1544161515-4ab6ce6db874'), 'silk sleep mask', 'A soft sleep mask that blocks room light without a tight strap.', 'Adjust the strap so it sits flat, not tight.', [
                ['title' => 'Light block', 'description' => 'Useful for naps and early mornings.'],
                ['title' => 'Soft band', 'description' => 'Sits over hair without pulling.'],
            ], [
                ['label' => 'Fabric', 'value' => 'Soft satin'],
                ['label' => 'Care', 'value' => 'Spot clean'],
            ]),
        ];
    }

    /** @param  list<array{title: string, description: string}>  $highlights
     * @param  list<array{label: string, value: string}>  $specs
     * @return array<string, mixed>
     */
    private function item(
        string $slug,
        string $name,
        string $category,
        float $price,
        float $old,
        string $badge,
        bool $flash,
        string $discount,
        int $claimed,
        int $units,
        string $image,
        string $hover,
        string $keyword,
        string $description,
        string $routine,
        array $highlights,
        array $specs,
    ): array {
        return [
            'slug' => $slug,
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'old_price' => $old,
            'badge' => $badge,
            'flash' => $flash,
            'discount' => $discount,
            'claimed' => $claimed,
            'units' => $units,
            'image' => $image,
            'hover' => $hover,
            'keyword' => $keyword,
            'description' => $description,
            'short' => $description,
            'routine' => $routine,
            'highlights' => $highlights,
            'specs' => $specs,
            'reviews' => [
                ['name' => 'Ayesha Khan', 'rating' => 5, 'title' => 'Exactly as described', 'body' => 'Arrived packed well in Lahore. The listing photos match what I opened, and cash on delivery was smooth.'],
                ['name' => 'Hassan Raza', 'rating' => 4, 'title' => 'Good everyday buy', 'body' => 'Using it for a week. Quality feels solid for the price and the size is what the page promised.'],
                ['name' => 'Mariam Siddiqui', 'rating' => 5, 'title' => 'Would order again', 'body' => 'Clear instructions on the page. Support answered on WhatsApp the same evening.'],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function posts(): array
    {
        $img = fn (string $id): string => 'https://images.unsplash.com/'.$id.'?auto=format&fit=crop&q=80&w=1200';
        $body = fn (string $lead, string $middle): string => '<p>'.$lead.'</p><p>'.$middle.'</p><p>Prices and stock on Quick Bazar can change. Check the product page before you checkout, and use cash on delivery where it is offered.</p>';

        return [
            [
                'slug' => '7-spectrum-led-therapy-routine',
                'title' => 'How to use a 7-spectrum LED mask at home',
                'category' => 'Beauty Tech',
                'read' => '6 min read',
                'date' => '2026-10-20',
                'image' => $img('photo-1598440947619-2c35fc9aa908'),
                'author' => 'Dr. Aliza Thorne',
                'role' => 'Skincare editor',
                'avatar' => 'AT',
                'likes' => 342,
                'snippet' => 'A simple at-home LED routine: clean skin, a 10-minute session, and sunscreen the next morning.',
                'keyword' => 'LED mask routine',
                'featured' => true,
                'content' => $body('Start with clean, dry skin. A 10-minute LED session three evenings a week is enough for most people trying a mask for the first time.', 'Skip heavy oils before the session so the light sits on skin, not on a film of product. In the morning, use moisturizer and sunscreen as usual.'),
            ],
            [
                'slug' => 'morning-skincare-order',
                'title' => 'The morning skincare order that actually fits a weekday',
                'category' => 'Skincare Secrets',
                'read' => '5 min read',
                'date' => '2026-10-12',
                'image' => $img('photo-1556228720-195a672e8a03'),
                'author' => 'Hira Qureshi',
                'role' => 'Beauty editor',
                'avatar' => 'HQ',
                'likes' => 218,
                'snippet' => 'Cleanser, treatment, moisturizer, sunscreen. Four steps, in that order, before you leave the house.',
                'keyword' => 'morning skincare routine',
                'featured' => false,
                'content' => $body('A weekday morning does not need ten bottles. Cleanse, apply one treatment such as vitamin C, seal it with moisturizer, and finish with sunscreen.', 'If your skin feels tight, the gel cream can replace a heavy cream. If it feels oily by noon, use a lighter layer, not more products.'),
            ],
            [
                'slug' => 'wash-day-hair-oil',
                'title' => 'Where to put hair oil so roots stay clean',
                'category' => 'Hair Care & Wellness',
                'read' => '4 min read',
                'date' => '2026-09-28',
                'image' => $img('photo-1519699047748-de8e457a634e'),
                'author' => 'Sana Malik',
                'role' => 'Hair editor',
                'avatar' => 'SM',
                'likes' => 176,
                'snippet' => 'Oil belongs on mid-lengths and ends. Here is a wash-day amount that does not flatten fine hair.',
                'keyword' => 'hair oil ends',
                'featured' => false,
                'content' => $body('Warm one or two drops between your palms. Press them onto the ends, then lightly over the mid-lengths. Leave the roots alone.', 'If you shampoo daily, oil after the hair is almost dry. On a weekly mask day, oil can wait until the next morning.'),
            ],
            [
                'slug' => 'choose-a-daytime-fragrance',
                'title' => 'How to choose a daytime fragrance in Karachi heat',
                'category' => 'Celebrity Routines',
                'read' => '4 min read',
                'date' => '2026-09-18',
                'image' => $img('photo-1541643600914-78b084683601'),
                'author' => 'Bilal Ahmed',
                'role' => 'Fragrance editor',
                'avatar' => 'BA',
                'likes' => 154,
                'snippet' => 'Citrus and clean musks stay polite in an office. Save oud for the evening.',
                'keyword' => 'daytime fragrance Pakistan',
                'featured' => false,
                'content' => $body('Heat makes fragrance louder. For the office, one spray of a citrus eau is enough. Pulse points, not a cloud over your clothes.', 'Keep attar and oud for evenings. A dab on the wrist travels further than people expect in a small room.'),
            ],
            [
                'slug' => 'five-minute-face-massage',
                'title' => 'A five-minute gua sha routine after serum',
                'category' => 'Hair Care & Wellness',
                'read' => '3 min read',
                'date' => '2026-09-04',
                'image' => $img('photo-1540555700478-4be289fbecef'),
                'author' => 'Hira Qureshi',
                'role' => 'Beauty editor',
                'avatar' => 'HQ',
                'likes' => 129,
                'snippet' => 'Serum first, then light upward strokes. No need to press hard.',
                'keyword' => 'gua sha routine',
                'featured' => false,
                'content' => $body('Apply a slippery serum so the stone glides. Sweep from the center of the face outward, then down the neck. Two minutes a side is plenty.', 'Rinse the stone and let it dry. A gua sha is a massage tool, not a substitute for sunscreen or a dermatologist.'),
            ],
            [
                'slug' => 'what-cash-on-delivery-covers',
                'title' => 'What to check before you pay cash on delivery',
                'category' => 'Skincare Secrets',
                'read' => '3 min read',
                'date' => '2026-08-22',
                'image' => $img('photo-1556228578-0d85b1a4d571'),
                'author' => 'Quick Bazar Desk',
                'role' => 'Store guide',
                'avatar' => 'QB',
                'likes' => 96,
                'snippet' => 'Match the parcel to your order number, then pay. Here is a 30-second check at the door.',
                'keyword' => 'cash on delivery Pakistan',
                'featured' => false,
                'content' => $body('When the rider arrives, match the name on the parcel with your order. Open the outer pack if the rider allows it and confirm the product name.', 'Pay the amount shown at checkout. If something is missing, note it before the rider leaves and message support with your order number.'),
            ],
        ];
    }
}
