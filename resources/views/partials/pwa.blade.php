<script>
    (function () {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {});
            });
        }

        var deferredPrompt = null;

        function isStandalone() {
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        }

        function isIos() {
            return /iphone|ipad|ipod/i.test(window.navigator.userAgent)
                || (window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1);
        }

        function isInAppBrowser() {
            return /FBAN|FBAV|Instagram|Line\/|WhatsApp|Twitter|Snapchat|MicroMessenger|; wv\)/i.test(window.navigator.userAgent);
        }

        function installButtons() {
            return document.querySelectorAll('[data-pwa-install]');
        }

        function showButtons(show) {
            installButtons().forEach(function (button) {
                button.classList.toggle('hidden', !show);
            });
        }

        function guideSteps() {
            if (isInAppBrowser()) {
                return [
                    'Buka halaman ini di browser utama (Chrome atau Safari), bukan di dalam aplikasi lain.',
                    'Setelah terbuka di browser, gunakan menu browser untuk memasang aplikasi.'
                ];
            }

            if (isIos()) {
                return [
                    'Buka aplikasi ini melalui Safari.',
                    'Ketuk tombol Bagikan (ikon kotak dengan panah ke atas).',
                    'Pilih "Tambah ke Layar Utama".',
                    'Ketuk "Tambah" untuk memasang.'
                ];
            }

            if (/Macintosh/i.test(window.navigator.userAgent)) {
                return [
                    'Buka browser Safari di Mac.',
                    'Pilih menu File > Add to Dock (Safari 17 atau lebih baru).',
                    'Konfirmasi untuk memasang aplikasi.'
                ];
            }

            if (/Firefox/i.test(window.navigator.userAgent) && !/Android/i.test(window.navigator.userAgent)) {
                return [
                    'Firefox di desktop belum mendukung pemasangan aplikasi web.',
                    'Buka aplikasi ini dengan Chrome, Edge, atau Safari, lalu pasang dari menu browser.'
                ];
            }

            if (/Android/i.test(window.navigator.userAgent)) {
                return [
                    'Buka menu browser (ikon tiga titik).',
                    'Pilih "Instal aplikasi" atau "Tambahkan ke Layar utama".',
                    'Konfirmasi untuk memasang.'
                ];
            }

            return [
                'Klik ikon instal di bilah alamat browser, atau buka menu browser.',
                'Pilih "Install" atau "Install app".',
                'Konfirmasi untuk memasang.'
            ];
        }

        function openGuide() {
            var overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 flex items-end sm:items-center justify-center bg-black/50 p-4';
            overlay.style.zIndex = '10000';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.innerHTML = [
                '<div class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-xl">',
                '<div class="flex items-start justify-between mb-4">',
                '<h2 class="text-[16px] font-bold text-gray-900">Pasang Sales Ledger</h2>',
                '<button type="button" data-pwa-close class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>',
                '</div>',
                '<ol class="space-y-2 text-[13px] text-gray-600 list-decimal pl-5">',
                guideSteps().map(function (step) { return '<li>' + step + '</li>'; }).join(''),
                '</ol>',
                '<button type="button" data-pwa-close class="mt-5 w-full bg-[#0F47A1] hover:bg-blue-900 text-white font-semibold py-3 rounded-xl text-[13px]">Mengerti</button>',
                '</div>'
            ].join('');

            document.body.appendChild(overlay);

            function close() {
                overlay.remove();
            }

            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) close();
            });

            overlay.querySelectorAll('[data-pwa-close]').forEach(function (button) {
                button.addEventListener('click', close);
            });
        }

        function install() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function () {
                    deferredPrompt = null;
                });
                return;
            }

            openGuide();
        }

        window.PwaInstall = {
            install: install,
            openGuide: openGuide,
            isStandalone: isStandalone
        };

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            deferredPrompt = event;
            showButtons(true);
        });

        window.addEventListener('appinstalled', function () {
            deferredPrompt = null;
            showButtons(false);
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (isStandalone()) {
                showButtons(false);
                return;
            }

            showButtons(true);

            installButtons().forEach(function (button) {
                if (button.dataset.pwaBound === '1') {
                    return;
                }

                button.dataset.pwaBound = '1';
                button.addEventListener('click', install);
            });
        });
    })();
</script>

@include('partials.push-notifications')
