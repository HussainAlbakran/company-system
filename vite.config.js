import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',

        rollupOptions: {
            input: {
                app: resolve(__dirname, 'resources/js/app.js'),
                styles: resolve(__dirname, 'resources/css/app.css'),
            },
        },
    },
});