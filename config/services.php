<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 90),
    ],

    'ai_seo' => [
        'default_provider' => env('AI_SEO_PROVIDER', 'smart'),
        'bulk_batch_size' => (int) env('AI_SEO_BULK_BATCH', 10),
    ],

    'product_import' => [
        /** Max products per category/URL listing run (0 in form = use this cap). */
        'listing_max' => (int) env('PRODUCT_IMPORT_LISTING_MAX', 10000),
        /** When import price mode is USD → PKR. */
        'usd_to_pkr' => (float) env('PRODUCT_IMPORT_USD_TO_PKR', 280),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
        'flash_model' => env('GEMINI_FLASH_MODEL', 'gemini-2.0-flash'),
        'url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 90),
    ],

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
        'url' => env('ANTHROPIC_API_URL', 'https://api.anthropic.com/v1/messages'),
        'version' => env('ANTHROPIC_API_VERSION', '2023-06-01'),
        'timeout' => (int) env('ANTHROPIC_TIMEOUT', 60),
    ],

];
