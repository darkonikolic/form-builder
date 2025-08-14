<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:3000; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:3000; connect-src 'self' http://localhost:3000 ws://localhost:3000 wss://localhost:3000; style-src 'self' 'unsafe-inline' http://localhost:3000 https://fonts.bunny.net; img-src 'self' data: blob:; font-src 'self' data: https://fonts.bunny.net; worker-src 'self' blob: data:;">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @if (app()->environment('local'))
            @viteReactRefresh
        @endif
        @vite(['resources/js/app.jsx'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
