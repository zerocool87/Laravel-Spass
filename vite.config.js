import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '192.168.10.123',
        port: 5173,
        hmr: {
            host: '192.168.10.123',
        },
        cors: true,
        headers: {
            'Access-Control-Allow-Origin': '*',
        },
    },
});
