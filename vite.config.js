import path from 'path'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { visualizer } from 'rollup-plugin-visualizer'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
        visualizer(),
    ],
    resolve: {
        preserveSymlinks: true,
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            root: path.resolve(__dirname, './'),
            node_modules: path.resolve(__dirname, './node_modules'),
            Vendor: path.resolve(__dirname, './vendor'),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
})
