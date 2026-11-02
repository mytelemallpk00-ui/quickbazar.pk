<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class LuminaProductDetailSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::query()->where('slug', 'lumina-pro-led-mask')->first();
        if (! $product) {
            return;
        }

        $product->update([
            'detail_heading' => 'Medical-Grade Photon Light Therapy Engineered for Your Skin',
            'detail_body' => 'The Lumina Pro 7-Spectrum LED Mask utilizes advanced narrowband light emitting diode technology to deliver precise wavelengths of light directly to cellular mitochondria. Endorsed by leading dermatologists and top celebrities, this mask provides spa-quality rejuvenation from the comfort of your home.',
            'detail_highlights' => [
                [
                    'title' => 'Collagen Synthesis (Red Light 630nm)',
                    'description' => 'Stimulates fibroblast proliferation to reduce fine lines and restore youthful elasticity.',
                ],
                [
                    'title' => 'Acne Clearance (Blue Light 415nm)',
                    'description' => 'Targets and neutralizes acne-causing bacteria deep within pores for spotless clarity.',
                ],
            ],
            'specs_heading' => 'Technical Specifications & Certifications',
            'specs_rows' => [
                ['label' => 'Wavelength Spectrum', 'value' => '7 targeted wavelengths (415nm to 830nm)'],
                ['label' => 'LED Bulb Count', 'value' => '240 medical-grade dual-core diodes'],
                ['label' => 'Battery & Power', 'value' => 'Rechargeable Li-ion 2500mAh (USB-C Fast Charge)'],
            ],
            'routine_heading' => 'Step-by-Step Skincare Routine',
            'routine_body' => 'Cleanse thoroughly, apply botanical serum, and wear the Lumina Pro Mask for 15 minutes 3-4 times a week for maximum natural glow.',
            'promo_label' => 'Save 30% Today',
            'units_left' => 4,
        ]);

        if ($product->productReviews()->exists()) {
            $product->refreshReviewAggregates();

            return;
        }

        ProductReview::query()->create([
            'product_id' => $product->id,
            'author_name' => 'Saboor Aly',
            'rating' => 5,
            'title' => 'Absolute game changer',
            'body' => 'Absolute game changer for my skin! I use it 3 times a week before shoots and my glow is unmatched.',
            'is_verified_buyer' => true,
            'is_published' => true,
            'created_at' => now()->subDays(2),
        ]);

        ProductReview::query()->create([
            'product_id' => $product->id,
            'author_name' => 'Ayesha Khan',
            'rating' => 5,
            'title' => 'Worth every rupee',
            'body' => 'Visible difference in texture within two weeks. The mask is comfortable and the app timers are very helpful.',
            'is_verified_buyer' => true,
            'is_published' => true,
            'created_at' => now()->subWeek(),
        ]);

        $product->refreshReviewAggregates();
    }
}
