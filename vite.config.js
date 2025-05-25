import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/lk.css',
                'resources/js/app.js',
              'resources/css/photo-editor.css',
            
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            // Добавляем алиасы для Bootstrap, если потребуется
            '~bootstrap': 'node_modules/bootstrap',
        },
    },
});
