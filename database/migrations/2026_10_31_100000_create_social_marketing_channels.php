<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_marketing_channels', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('label');
            $table->text('guide')->nullable();
            $table->json('credential_fields')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->boolean('supports_scheduling')->default(false);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            [
                'key' => 'facebook',
                'label' => 'Facebook Page & Feed',
                'guide' => 'Meta Developer → Graph API: Page ID aur long-lived Page Access Token (Page posts).',
                'credential_fields' => json_encode([
                    ['key' => 'app_id', 'label' => 'App ID', 'secret' => false, 'required' => false],
                    ['key' => 'app_secret', 'label' => 'App Secret', 'secret' => true, 'required' => false],
                    ['key' => 'page_id', 'label' => 'Page ID', 'secret' => false, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Page Access Token', 'secret' => true, 'required' => true],
                ]),
                'sort_order' => 10,
                'is_enabled' => true,
                'supports_scheduling' => true,
            ],
            [
                'key' => 'facebook_marketplace',
                'label' => 'Facebook Marketplace',
                'guide' => 'Commerce Manager / Marketplace listings — catalog ID aur Page token jo marketplace listings manage kare.',
                'credential_fields' => json_encode([
                    ['key' => 'catalog_id', 'label' => 'Catalog ID', 'secret' => false, 'required' => true],
                    ['key' => 'page_id', 'label' => 'Page ID', 'secret' => false, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true, 'required' => true],
                ]),
                'sort_order' => 20,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'instagram',
                'label' => 'Instagram Business',
                'guide' => 'Instagram Business account ID + Meta access token (Facebook Page se linked).',
                'credential_fields' => json_encode([
                    ['key' => 'business_account_id', 'label' => 'Business Account ID', 'secret' => false, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true, 'required' => true],
                ]),
                'sort_order' => 30,
                'is_enabled' => true,
                'supports_scheduling' => true,
            ],
            [
                'key' => 'linkedin',
                'label' => 'LinkedIn Company Page',
                'guide' => 'LinkedIn Developer app: access token + company Organization URN (urn:li:organization:…).',
                'credential_fields' => json_encode([
                    ['key' => 'client_id', 'label' => 'Client ID', 'secret' => false, 'required' => false],
                    ['key' => 'client_secret', 'label' => 'Client Secret', 'secret' => true, 'required' => false],
                    ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true, 'required' => true],
                    ['key' => 'organization_urn', 'label' => 'Organization URN', 'secret' => false, 'required' => true],
                ]),
                'sort_order' => 40,
                'is_enabled' => true,
                'supports_scheduling' => true,
            ],
            [
                'key' => 'youtube',
                'label' => 'YouTube (Long-form)',
                'guide' => 'Google Cloud → YouTube Data API → OAuth refresh token. Scheduler par video upload zaroori.',
                'credential_fields' => json_encode([
                    ['key' => 'client_id', 'label' => 'Client ID', 'secret' => false, 'required' => false],
                    ['key' => 'client_secret', 'label' => 'Client Secret', 'secret' => true, 'required' => false],
                    ['key' => 'refresh_token', 'label' => 'Refresh Token', 'secret' => true, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Access Token (optional)', 'secret' => true, 'required' => false],
                    ['key' => 'channel_id', 'label' => 'Channel ID', 'secret' => false, 'required' => false],
                ]),
                'sort_order' => 50,
                'is_enabled' => true,
                'supports_scheduling' => true,
            ],
            [
                'key' => 'youtube_shorts_tiktok',
                'label' => 'YouTube Shorts / TikTok',
                'guide' => 'Short-form: TikTok Developer app credentials + optional YouTube Shorts default playlist / channel.',
                'credential_fields' => json_encode([
                    ['key' => 'tiktok_client_key', 'label' => 'TikTok Client Key', 'secret' => false, 'required' => true],
                    ['key' => 'tiktok_client_secret', 'label' => 'TikTok Client Secret', 'secret' => true, 'required' => true],
                    ['key' => 'youtube_channel_id', 'label' => 'YouTube Channel ID (Shorts)', 'secret' => false, 'required' => false],
                    ['key' => 'refresh_token', 'label' => 'YouTube Refresh Token (Shorts)', 'secret' => true, 'required' => false],
                ]),
                'sort_order' => 60,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'pinterest',
                'label' => 'Pinterest Marketing',
                'guide' => 'Pinterest Developer app → OAuth access token + ad account / board ID for pins.',
                'credential_fields' => json_encode([
                    ['key' => 'app_id', 'label' => 'App ID', 'secret' => false, 'required' => true],
                    ['key' => 'app_secret', 'label' => 'App Secret', 'secret' => true, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true, 'required' => true],
                    ['key' => 'board_id', 'label' => 'Default Board ID', 'secret' => false, 'required' => false],
                ]),
                'sort_order' => 70,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'google_business_profile',
                'label' => 'Google Business Profile',
                'guide' => 'Google Business Profile API — location ID + OAuth access token (posts & updates).',
                'credential_fields' => json_encode([
                    ['key' => 'location_id', 'label' => 'Location ID', 'secret' => false, 'required' => true],
                    ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true, 'required' => true],
                    ['key' => 'account_id', 'label' => 'Account ID (optional)', 'secret' => false, 'required' => false],
                ]),
                'sort_order' => 80,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'google_shopping',
                'label' => 'Google Shopping',
                'guide' => 'Merchant Center — Merchant ID + Content API credentials for product feeds.',
                'credential_fields' => json_encode([
                    ['key' => 'merchant_id', 'label' => 'Merchant ID', 'secret' => false, 'required' => true],
                    ['key' => 'content_api_token', 'label' => 'Content API Token', 'secret' => true, 'required' => true],
                ]),
                'sort_order' => 90,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'referral_program',
                'label' => 'Referral Program',
                'guide' => 'Internal / partner referral links — program dashboard URL + API key if automated tracking use karte hain.',
                'credential_fields' => json_encode([
                    ['key' => 'program_url', 'label' => 'Program dashboard URL', 'secret' => false, 'required' => true],
                    ['key' => 'api_key', 'label' => 'API / webhook key', 'secret' => true, 'required' => false],
                ]),
                'sort_order' => 100,
                'is_enabled' => true,
                'supports_scheduling' => false,
            ],
            [
                'key' => 'telegram',
                'label' => 'Telegram Channel',
                'guide' => '@BotFather se bot token, phir channel/chat ID (@username ya numeric).',
                'credential_fields' => json_encode([
                    ['key' => 'bot_token', 'label' => 'Bot Token', 'secret' => true, 'required' => true],
                    ['key' => 'chat_id', 'label' => 'Chat / Channel ID', 'secret' => false, 'required' => true],
                ]),
                'sort_order' => 110,
                'is_enabled' => true,
                'supports_scheduling' => true,
            ],
        ];

        foreach ($rows as $row) {
            DB::table('social_marketing_channels')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_marketing_channels');
    }
};
