import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 3000,
        strictPort: true,
        origin: 'http://localhost:3000',
        cors: {
            origin: ['http://localhost:8085', 'http://localhost:3000'],
            credentials: true,
        },
        hmr: {
            host: 'localhost',
            port: 3000,
            protocol: 'ws',
            clientPort: 3000,
        },
        watch: {
            ignored: ['**/storage/**', '**/public/storage/**'],
        },
    },
});
