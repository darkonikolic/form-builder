<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\EloquentFieldRepository;
use App\Repositories\EloquentFormRepository;
use App\Repositories\FieldRepositoryInterface;
use App\Repositories\FormRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(FormRepositoryInterface::class, EloquentFormRepository::class);
        $this->app->bind(FieldRepositoryInterface::class, EloquentFieldRepository::class);
    }
}
