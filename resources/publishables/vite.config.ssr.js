import path from 'path'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// POC: builds the SSR render server to bootstrap/ssr/server.js
// Run it with: node bootstrap/ssr/server.js
export default defineConfig({
    plugins: [vue()],
    resolve: {
        preserveSymlinks: true,
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            Vendor: path.resolve(__dirname, './vendor'),
        },
    },
    build: {
        ssr: 'vendor/rapidez/core/resources/js/ssr/server.js',
        outDir: 'bootstrap/ssr',
        emptyOutDir: true,
    },
    ssr: {
        // Bundle everything except Vue itself, so there's one Vue instance
        // and it includes the template compiler for the Blade templates.
        noExternal: true,
        external: ['vue', '@vue/server-renderer', 'happy-dom'],
    },
})
