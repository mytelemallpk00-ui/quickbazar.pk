<?php

namespace Database\Seeders;

use App\Models\ChatbotRule;
use Illuminate\Database\Seeder;

class ChatbotRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['keyword' => 'hello', 'response' => 'Hello! Welcome to Beautage. How can I help you today — shopping, checkout, or order tracking?'],
            ['keyword' => 'salam', 'response' => 'Wa alaikum assalam! Beautage mein khush amdeed. Products, checkout ya delivery ke baray mein pooch sakte hain.'],
            ['keyword' => 'shipping', 'response' => 'We deliver across Pakistan via trusted couriers (TCS, Leopards, M&P). Type "checkout" to place an order or "track" for order status.'],
            ['keyword' => 'return', 'response' => 'Returns policy ke liye humari Returns page dekhein ya WhatsApp par team se rabta karein — hum 30-day support offer karte hain eligible items par.'],
            ['keyword' => 'price', 'response' => 'Product ka naam likhein (jaise "vitamin c serum price") — main database se live price aur product link bhej dunga.'],
            ['keyword' => 'discount', 'response' => 'Checkout par promo try karein (e.g. BEAUTAGE15). Type "checkout" for direct secure checkout link.'],
            ['keyword' => 'bundle', 'response' => 'Type "bundle deals" — main live combos, prices aur links bhej dunga. Exclusive Offers page par saari deals hain.'],
        ];

        foreach ($rules as $rule) {
            ChatbotRule::query()->updateOrCreate(
                ['keyword' => $rule['keyword']],
                ['response' => $rule['response'], 'is_active' => true],
            );
        }
    }
}
