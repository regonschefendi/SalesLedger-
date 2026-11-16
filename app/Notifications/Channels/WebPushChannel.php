<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable instanceof User || ! method_exists($notification, 'toWebPush')) {
            return;
        }

        $payload = $notification->toWebPush($notifiable);

        if (! is_array($payload) || $payload === []) {
            return;
        }

        // Kirim setelah response selesai agar proses bisnis tidak menunggu
        // jaringan ke layanan push.
        defer(fn () => app(WebPushService::class)->sendToUser($notifiable, $payload));
    }
}
