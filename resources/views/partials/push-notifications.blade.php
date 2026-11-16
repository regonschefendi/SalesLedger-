@auth
    @php($vapidPublicKey = config('services.webpush.public_key'))

    @if($vapidPublicKey)
        <script>
            (function () {
                var publicKey = @json($vapidPublicKey);
                var csrfToken = @json(csrf_token());

                var state = {
                    supported: 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window,
                    busy: false,
                    subscription: null
                };

                function isStandalone() {
                    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                }

                function isIos() {
                    return /iphone|ipad|ipod/i.test(window.navigator.userAgent)
                        || (window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1);
                }

                function buttons() {
                    return document.querySelectorAll('[data-push-toggle]');
                }

                function urlBase64ToUint8Array(base64String) {
                    var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
                    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    var rawData = window.atob(base64);
                    var output = new Uint8Array(rawData.length);

                    for (var i = 0; i < rawData.length; i++) {
                        output[i] = rawData.charCodeAt(i);
                    }

                    return output;
                }

                function supportedContentEncoding() {
                    var supported = window.PushManager && window.PushManager.supportedContentEncodings;

                    if (supported && supported.indexOf('aes128gcm') !== -1) {
                        return 'aes128gcm';
                    }

                    return 'aesgcm';
                }

                function render() {
                    buttons().forEach(function (button) {
                        var label = button.querySelector('[data-push-label]');
                        var status = button.querySelector('[data-push-status]');

                        if (!state.supported) {
                            button.classList.add('hidden');
                            return;
                        }

                        button.classList.remove('hidden');
                        button.disabled = state.busy;

                        if (state.busy) {
                            label.textContent = 'Menyiapkan...';
                            status.textContent = 'Mohon tunggu sebentar.';
                            return;
                        }

                        if (Notification.permission === 'denied') {
                            label.textContent = 'Notifikasi Diblokir';
                            status.textContent = 'Izinkan notifikasi lewat pengaturan browser.';
                            return;
                        }

                        if (isIos() && !isStandalone()) {
                            label.textContent = 'Aktifkan Notifikasi';
                            status.textContent = 'Pasang aplikasi ke Layar Utama terlebih dahulu.';
                            return;
                        }

                        if (state.subscription) {
                            label.textContent = 'Notifikasi Aktif';
                            status.textContent = 'Ketuk untuk mematikan notifikasi di luar aplikasi.';
                            return;
                        }

                        label.textContent = 'Aktifkan Notifikasi';
                        status.textContent = 'Tampilkan notifikasi di luar aplikasi, termasuk saat HP terkunci.';
                    });
                }

                function pushSubscribePayload(subscription) {
                    var json = subscription.toJSON();

                    return {
                        endpoint: json.endpoint,
                        keys: json.keys,
                        contentEncoding: supportedContentEncoding()
                    };
                }

                function sendSubscription(subscription) {
                    return fetch('/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(pushSubscribePayload(subscription))
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Gagal menyimpan langganan notifikasi.');
                        }

                        return response;
                    });
                }

                function refresh() {
                    if (!state.supported) {
                        render();
                        return Promise.resolve();
                    }

                    return navigator.serviceWorker.ready
                        .then(function (registration) {
                            return registration.pushManager.getSubscription();
                        })
                        .then(function (subscription) {
                            state.subscription = subscription;

                            // Sinkronkan ulang agar perangkat ini terhubung ke akun
                            // yang sedang login (misalnya setelah berganti akun).
                            if (subscription) {
                                sendSubscription(subscription).catch(function () {});
                            }
                        })
                        .catch(function () {
                            state.subscription = null;
                        })
                        .then(render);
                }

                function enable() {
                    if (state.busy) {
                        return;
                    }

                    if (isIos() && !isStandalone()) {
                        if (window.PwaInstall) {
                            window.PwaInstall.openGuide();
                        }
                        return;
                    }

                    state.busy = true;
                    render();

                    Notification.requestPermission()
                        .then(function (permission) {
                            if (permission !== 'granted') {
                                throw new Error('Izin notifikasi tidak diberikan.');
                            }

                            return navigator.serviceWorker.ready;
                        })
                        .then(function (registration) {
                            return registration.pushManager.getSubscription().then(function (subscription) {
                                if (subscription) {
                                    return subscription;
                                }

                                return registration.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: urlBase64ToUint8Array(publicKey)
                                });
                            });
                        })
                        .then(function (subscription) {
                            state.subscription = subscription;

                            return sendSubscription(subscription);
                        })
                        .catch(function (error) {
                            window.alert(error.message || 'Notifikasi tidak dapat diaktifkan.');
                        })
                        .then(function () {
                            state.busy = false;

                            return refresh();
                        });
                }

                function disable() {
                    if (state.busy || !state.subscription) {
                        return;
                    }

                    state.busy = true;
                    render();

                    var subscription = state.subscription;

                    fetch('/push/unsubscribe', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ endpoint: subscription.endpoint })
                    })
                        .catch(function () {})
                        .then(function () {
                            return subscription.unsubscribe();
                        })
                        .catch(function (error) {
                            window.alert(error.message || 'Notifikasi tidak dapat dimatikan.');
                        })
                        .then(function () {
                            state.subscription = null;
                            state.busy = false;

                            return refresh();
                        });
                }

                function toggle() {
                    if (state.subscription) {
                        disable();
                        return;
                    }

                    enable();
                }

                function bind() {
                    buttons().forEach(function (button) {
                        if (button.dataset.pushBound === '1') {
                            return;
                        }

                        button.dataset.pushBound = '1';
                        button.addEventListener('click', toggle);
                    });

                    if (state.supported) {
                        refresh();
                    } else {
                        render();
                    }
                }

                document.addEventListener('DOMContentLoaded', bind);
            })();
        </script>
    @endif
@endauth
