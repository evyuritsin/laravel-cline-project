<?php

return [
    // Yandex Object Storage (S3-compatible)
    'storage' => [
        'endpoint' => env('YANDEX_STORAGE_ENDPOINT', 'https://storage.yandexcloud.net'),
        'region' => env('YANDEX_STORAGE_REGION', 'ru-central1'),
        'bucket' => env('YANDEX_STORAGE_BUCKET', ''),
        'key' => env('YANDEX_STORAGE_KEY', ''),
        'secret' => env('YANDEX_STORAGE_SECRET', ''),
        'url' => env('YANDEX_STORAGE_URL', ''),
    ],

    // Yandex Geolocation API
    'geolocation' => [
        'key' => env('YANDEX_GEOLOCATION_KEY', ''),
    ],

    // Yandex Maps API
    'maps' => [
        'key' => env('YANDEX_MAPS_KEY', ''),
    ],
];