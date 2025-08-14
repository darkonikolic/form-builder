<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

// Swagger API Documentation
Route::get('/api/documentation', function () {
    return view('swagger-ui');
});

Route::get('/api/documentation.json', function () {
    return response()->json(json_decode(file_get_contents(storage_path('api-docs/api-docs.json'))));
});

// Swagger UI Assets Route
Route::get('/swagger-assets/{file}', function ($file) {
    $filePath = base_path("vendor/swagger-api/swagger-ui/dist/{$file}");

    if (!file_exists($filePath)) {
        abort(404);
    }

    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'html' => 'text/html',
    ];

    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

    return response()->file($filePath, ['Content-Type' => $mimeType]);
})->where('file', '.*');
