<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

use function Pest\Laravel\get;

test('homepage loads successfully', function () {
    get('/')->assertStatus(200);
});

test('form builder page loads', function () {
    get('/form-builder')->assertStatus(200);
});

test('api test endpoint works', function () {
    get('/test')->assertStatus(200);
});

test('database connection works', function () {
    expect(DB::connection()->getPdo())->not->toBeNull();
});

test('laravel version is correct', function () {
    expect(app()->version())->toContain('12');
});

test('php version is 8.2 or higher', function () {
    expect(PHP_VERSION_ID)->toBeGreaterThanOrEqual(80200);
});

test('pest is working correctly', function () {
    expect(true)->toBeTrue();
    expect(2 + 2)->toBe(4);
    expect('hello')->toBeString();
});

test('form builder environment is ready', function () {
    expect(config('app.name'))->toBe('Laravel');
    expect(config('database.default'))->toBe('sqlite'); // Laravel default
    expect(config('app.env'))->toBe('testing'); // Environment
    expect(app()->environment())->toBe('testing'); // Environment check
});

test('basic routing works', function () {
    // Test that we can make requests
    $response = get('/');
    expect($response->status())->toBe(200);
});

test('artisan commands are available', function () {
    // Test that Laravel artisan is working
    $output = shell_exec('php artisan --version');
    expect($output)->toContain('Laravel Framework');
});
