import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'],
            refresh: true,
            fonts: [
                bunny('Playfair Display', {
                    weights: [400, 500, 600, 700],
                    styles: ['normal', 'italic'],
                }),
                bunny('Inter', {
                    weights: [300, 400, 500, 600],
                }),
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
