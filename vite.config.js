import path from 'path'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'
import { visualizer } from 'rollup-plugin-visualizer'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
        visualizer(),
    ],
    resolve: {
        preserveSymlinks: true,
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'Vendor': path.resolve(__dirname, './vendor'),
            'vue': path.resolve(__dirname, './node_modules/vue/dist/vue.esm.js')
        }
    },
    build: {
        commonjsOptions: {
            // Since vue-slider-component is not esm and receives vue esm this is required.
            requireReturnsDefault: 'preferred',
        },
    },
});
