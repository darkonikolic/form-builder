<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Vite Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Vite development server settings.
    |
    */

    'dev_server_url' => env('VITE_DEV_SERVER_URL', 'http://localhost:3000'),

    'manifest_path' => env('VITE_MANIFEST_PATH', public_path('build/manifest.json')),

    'hot_file' => env('VITE_HOT_FILE', public_path('hot')),
];
