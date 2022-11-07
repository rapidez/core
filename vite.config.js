import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { createVuePlugin } from 'vite-plugin-vue2'
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
        createVuePlugin(),
        visualizer(),
    ],
    define: {
        'process.env': process.env,
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'Vendor': '/vendor',
            'vue': 'vue/dist/vue.js',
            '@vueuse/core': '@vueuse/core'
        }
    }
});
