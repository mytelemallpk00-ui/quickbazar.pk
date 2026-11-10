<?php

return [
    'origin_city' => env('COURIER_ORIGIN_CITY', 'Lahore'),

    'default_weight_kg' => (float) env('COURIER_DEFAULT_WEIGHT_KG', 0.5),

    'weight_per_item_kg' => (float) env('COURIER_WEIGHT_PER_ITEM_KG', 0.35),

    'smart_shipping_enabled' => false,

    'platforms' => [
        'tcs' => [
            'rate_url' => env('TCS_RATE_URL'),
            'book_url' => env('TCS_BOOK_URL'),
            'track_url' => env('TCS_TRACK_URL'),
        ],
        'leopards' => [
            'rate_url' => env('LEOPARDS_RATE_URL'),
            'book_url' => env('LEOPARDS_BOOK_URL'),
            'track_url' => env('LEOPARDS_TRACK_URL'),
        ],
        'blueex' => [
            'rate_url' => env('BLUEEX_RATE_URL'),
            'book_url' => env('BLUEEX_BOOK_URL'),
            'track_url' => env('BLUEEX_TRACK_URL'),
        ],
        'mnp' => [
            'rate_url' => env('MNP_RATE_URL'),
            'book_url' => env('MNP_BOOK_URL', 'https://mnpcourier.com/mycodapi/api/Booking/InsertBookingData'),
            'track_url' => env('MNP_TRACK_URL', 'https://tracking.mulphilog.com.pk/api/CNTracking'),
            'default_insert_type' => (int) env('MNP_INSERT_TYPE', 19),
            'portal_url' => 'https://mnpcourier.com/cplight/login',
            'api_docs_url' => 'https://mnpcourier.com/mycodapi/swagger/ui/index',
        ],
    ],
];
