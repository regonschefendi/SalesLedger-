self.addEventListener('push', function (event) {
    var payload = {};

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (error) {
            payload = { body: event.data.text() };
        }
    }

    var title = payload.title || 'Sales Ledger';
    var url = payload.url || '/notifications';

    var options = {
        body: payload.body || payload.message || 'Ada notifikasi baru.',
        icon: payload.icon || '/icons/icon-192x192.png',
        badge: payload.badge || '/icons/icon-192x192.png',
        tag: payload.tag || 'sales-ledger',
        renotify: true,
        vibrate: [200, 100, 200],
        data: { url: url }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    var url = event.notification.data && event.notification.data.url
        ? event.notification.data.url
        : '/notifications';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windows) {
            for (var i = 0; i < windows.length; i++) {
                if (windows[i].url === url && 'focus' in windows[i]) {
                    return windows[i].focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
