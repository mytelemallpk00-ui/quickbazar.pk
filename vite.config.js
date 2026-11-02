import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/storefront.css'],
            refresh: ['resources/views/**', 'public/js/**'],
        }),
        tailwindcss(),
    ],
    build: {
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
