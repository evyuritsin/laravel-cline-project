<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Yandex Object Storage (S3-compatible)
        'yandex' => [
            'driver' => 's3',
            'endpoint' => env('YANDEX_STORAGE_ENDPOINT', 'https://storage.yandexcloud.net'),
            'region' => env('YANDEX_STORAGE_REGION', 'ru-central1'),
            'bucket' => env('YANDEX_STORAGE_BUCKET', ''),
            'key' => env('YANDEX_STORAGE_KEY', ''),
            'secret' => env('YANDEX_STORAGE_SECRET', ''),
            'url' => env('YANDEX_STORAGE_URL', ''),
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
