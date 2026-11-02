<?php

return [

    'platforms' => [
        'facebook' => [
            'app_id' => env('SOCIAL_FACEBOOK_APP_ID'),
            'app_secret' => env('SOCIAL_FACEBOOK_APP_SECRET'),
            'page_id' => env('SOCIAL_FACEBOOK_PAGE_ID'),
            'access_token' => env('SOCIAL_FACEBOOK_ACCESS_TOKEN'),
        ],
        'instagram' => [
            'business_account_id' => env('SOCIAL_INSTAGRAM_BUSINESS_ACCOUNT_ID'),
            'access_token' => env('SOCIAL_INSTAGRAM_ACCESS_TOKEN'),
        ],
        'linkedin' => [
            'client_id' => env('SOCIAL_LINKEDIN_CLIENT_ID'),
            'client_secret' => env('SOCIAL_LINKEDIN_CLIENT_SECRET'),
            'access_token' => env('SOCIAL_LINKEDIN_ACCESS_TOKEN'),
            'organization_urn' => env('SOCIAL_LINKEDIN_ORGANIZATION_URN'),
        ],
        'youtube' => [
            'client_id' => env('SOCIAL_YOUTUBE_CLIENT_ID'),
            'client_secret' => env('SOCIAL_YOUTUBE_CLIENT_SECRET'),
            'refresh_token' => env('SOCIAL_YOUTUBE_REFRESH_TOKEN'),
            'channel_id' => env('SOCIAL_YOUTUBE_CHANNEL_ID'),
        ],
        'telegram' => [
            'bot_token' => env('SOCIAL_TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('SOCIAL_TELEGRAM_CHAT_ID'),
        ],
    ],

    'openai' => [
        'model' => env('SOCIAL_OPENAI_MODEL', env('OPENAI_MODEL', 'gpt-4o-mini')),
    ],

    /** Platforms with automated publish drivers (Post scheduler). */
    'auto_publishers' => [
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
        'telegram',
    ],

];
