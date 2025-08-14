<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

use function Pest\Laravel\get;

test('homepage loads successfully', function (): void {
    get('/')->assertStatus(200);
});

test('database connection works', function (): void {
    expect(DB::connection()->getPdo())->not->toBeNull();
});

test('laravel version is correct', function (): void {
    expect(app()->version())->toContain('12');
});

test('php version is 8.2 or higher', function (): void {
    expect(PHP_VERSION_ID)->toBeGreaterThanOrEqual(80200);
});

test('pest is working correctly', function (): void {
    expect(true)->toBeTrue();
    expect(2 + 2)->toBe(4);
    expect('hello')->toBeString();
});

test('form builder environment is ready', function (): void {
    expect(config('app.name'))->toBe('Laravel');
    expect(config('database.default'))->toBe('sqlite');
    expect(config('app.env'))->toBe('testing');
    expect(app()->environment())->toBe('testing');
});

test('basic routing works', function (): void {
    $response = get('/');
    expect($response->status())->toBe(200);
});

test('artisan commands are available', function (): void {
    $output = shell_exec('php artisan --version');
    expect($output)->toContain('Laravel Framework');
});
