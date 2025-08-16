<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ViteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enable Vite development server access
        if (app()->environment('local', 'development')) {
            // Set Vite manifest path
            config(['vite.manifest_path' => public_path('build/manifest.json')]);

            // Set Vite dev server URL
            config(['vite.dev_server_url' => 'http://node:3000']);
        }

        return $next($request);
    }
}
