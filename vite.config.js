import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa'; // 1. Import plugin PWA

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        
        // 2. Settingan PWA
        VitePWA({
            outDir: 'public', // Output ditaruh di folder public biar Laravel gampang bacanya
            buildBase: '/',
            scope: '/',
            injectRegister: null, // Kita register manual di Blade biar aman
            manifest: {
                name: 'Sales Ledger App',
                short_name: 'SalesLedger',
                description: 'Aplikasi Scan Faktur AI untuk Tim Sales',
                theme_color: '#4A89F3', // Warna biru tema lu
                background_color: '#F8F9FE',
                display: 'standalone', // Bikin tampilan full screen tanpa address bar (Native feel)
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
                // SECURITY SETTING:
                navigateFallback: '/offline', // Kalau ga ada sinyal internet, paksa masuk ke halaman Offline
                globPatterns: ['build/assets/**/*.{js,css,png,svg,ico}'], // Cuma simpan file styling
                cleanupOutdatedCaches: true,
                runtimeCaching: [
                    {
                        // 1. Dilarang keras nge-cache route API dan submit data! (Network Only)
                        urlPattern: /\/(api|faktur)\/.*/i,
                        handler: 'NetworkOnly', 
                    },
                    {
                        // 2. File statis luar (kayak font Google) boleh di-cache biar cepet
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: { cacheName: 'google-fonts-cache' }
                    }
                ]
            }
        })
    ],
});