<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushService
{
    public function isConfigured(): bool
    {
        return filled(config('services.webpush.public_key'))
            && filled(config('services.webpush.private_key'));
    }

    public function publicKey(): ?string
    {
        return $this->isConfigured() ? config('services.webpush.public_key') : null;
    }

    public function subscribe(User $user, array $payload): PushSubscription
    {
        $endpoint = (string) $payload['endpoint'];
        $keys = $payload['keys'] ?? [];

        // Satu perangkat hanya boleh terhubung ke satu akun agar notifikasi
        // akun sebelumnya tidak ikut terkirim setelah berganti akun.
        PushSubscription::query()
            ->where('endpoint', $endpoint)
            ->where('user_id', '!=', $user->getKey())
            ->delete();

        return $user->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'public_key' => $keys['p256dh'] ?? null,
                'auth_token' => $keys['auth'] ?? null,
                'content_encoding' => $payload['contentEncoding'] ?? 'aesgcm',
            ]
        );
    }

    public function unsubscribe(User $user, string $endpoint): void
    {
        $user->pushSubscriptions()->where('endpoint', $endpoint)->delete();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function sendToUser(User $user, array $payload): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $subscriptions = $user->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => (string) config('services.webpush.subject'),
                    'publicKey' => (string) config('services.webpush.public_key'),
                    'privateKey' => (string) config('services.webpush.private_key'),
                ],
            ], [
                'TTL' => 86400,
                'urgency' => 'high',
            ], new Client([
                'timeout' => (float) config('services.webpush.timeout', 15),
                'connect_timeout' => 5,
            ]));

            $message = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            foreach ($subscriptions as $subscription) {
                $webPush->queueNotification(
                    $subscription->toWebPushSubscription(),
                    $message,
                    ['TTL' => 86400, 'urgency' => 'high']
                );
            }

            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    continue;
                }

                if ($report->isSubscriptionExpired()) {
                    PushSubscription::query()
                        ->where('endpoint', $report->getEndpoint())
                        ->delete();

                    continue;
                }

                Log::warning('Gagal mengirim web push.', [
                    'endpoint' => $report->getEndpoint(),
                    'reason' => $report->getReason(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Web push tidak dapat dikirim.', [
                'user_id' => $user->getKey(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
