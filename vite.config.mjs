import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    resolve: {
        alias: [
            {
                find: /^vue$/,
                replacement: 'vue/dist/vue.esm-bundler.js',
            },
        ],
    },
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/js/legacy-app.js',
                'resources/assets/js/app.js',
                'resources/assets/js/fichar-app.js',
                'resources/assets/js/ppIntranet.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
