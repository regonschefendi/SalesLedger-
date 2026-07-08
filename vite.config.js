import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    // set deployment ke netlify
    build: {
        outDir: "dist"
    },

    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        
        // Settingan PWA
        VitePWA({
            outDir: 'public', 
            base: '/build/',
            scope: '/',
            injectRegister: null, 
            manifest: {
                id: '/',
                start_url: '/',
                name: 'Sales Ledger App',
                short_name: 'SalesLedger',
                description: 'Aplikasi Scan Faktur AI untuk Tim Sales',
                theme_color: '#4A89F3',
                background_color: '#F8F9FE',
                display: 'standalone',
                icons: [
                    {
                        src: '/icons/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/icons/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            },
            workbox: {
                navigateFallback: '/offline.html', 
                
                globPatterns: ['build/assets/**/*.{js,css,png,svg,ico}', 'offline.html'],
                cleanupOutdatedCaches: true,
                runtimeCaching: [
                    {
                        urlPattern: /\/(api|faktur)\/.*/i,
                        handler: 'NetworkOnly', 
                    },
                    {
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: { cacheName: 'google-fonts-cache' }
                    }
                ]
            }
        })
    ],
});