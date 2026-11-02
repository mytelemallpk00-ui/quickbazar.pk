<?php

return [
    'service_account_path' => storage_path('app/google/service-account.json'),
    'scope' => 'https://www.googleapis.com/auth/indexing',
    'publish_url' => 'https://indexing.googleapis.com/v3/urlNotifications:publish',
    'token_url' => 'https://oauth2.googleapis.com/token',
    'daily_quota_hint' => (int) env('GOOGLE_INDEXING_DAILY_QUOTA', 200),
];
