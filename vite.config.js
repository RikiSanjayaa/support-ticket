import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    server: {
        https: false,
        host: true,
        hmr: {
            host: 'localhost',
        },
    },
});
