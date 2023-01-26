import path from 'path';
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { createVuePlugin } from 'vite-plugin-vue2'
import { visualizer } from 'rollup-plugin-visualizer'

export default ({ mode }) => {
    let vueProduction = process.env?.NODE_ENV === 'production' ? '.min' : '';

    return defineConfig({
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js'
                ],
                refresh: true,
            }),
            createVuePlugin(),
            visualizer(),
        ],
        resolve: {
            preserveSymlinks: true,
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'Vendor': path.resolve(__dirname, './vendor'),
                'vue': path.resolve(__dirname, `./node_modules/vue/dist/vue${vueProduction}.js`)
            },
        }
    });
}
