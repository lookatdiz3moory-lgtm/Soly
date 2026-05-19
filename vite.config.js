import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/components.css',
                'resources/css/booking.css',
                'resources/js/app.js',
                'resources/js/components.js',
                'resources/js/booking.js',
            ],
            refresh: true,
        }),
    ],
    server: { host: 'localhost', port: 5173, strictPort: false },
    build:  { manifest: true, outDir: 'public/build' },
    css:    { devSourcemap: true },
});
