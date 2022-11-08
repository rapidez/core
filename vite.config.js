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
            '@': path.resolve(__dirname, './resources/js'),
            'node_modules': path.resolve(__dirname, './node_modules'),
            'Vendor': path.resolve(__dirname, './vendor'),
            'vue': path.resolve(__dirname, './node_modules/vue/dist/vue.js')
        }
    }
});
