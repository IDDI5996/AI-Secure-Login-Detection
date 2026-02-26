import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

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
    // Optional: Optimizations
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs'],
                    'charts': ['chart.js'],
                }
            }
        }
    }
})