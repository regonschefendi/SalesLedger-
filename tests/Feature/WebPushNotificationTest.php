<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use App\Notifications\Channels\WebPushChannel;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function subscriptionPayload(string $endpoint = 'https://push.example.com/subscription/123'): array
    {
        return [
            'endpoint' => $endpoint,
            'keys' => [
                'p256dh' => 'test-public-key',
                'auth' => 'test-auth-token',
            ],
            'contentEncoding' => 'aesgcm',
        ];
    }

    public function test_authenticated_user_can_subscribe_to_push_notifications(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/push/subscribe', $this->subscriptionPayload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.com/subscription/123',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);
    }

    public function test_push_subscription_moves_to_the_account_currently_using_the_device(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $payload = $this->subscriptionPayload();

        $this->actingAs($firstUser)->postJson('/push/subscribe', $payload)->assertOk();
        $this->actingAs($secondUser)->postJson('/push/subscribe', $payload)->assertOk();

        $this->assertSame(1, PushSubscription::count());
        $this->assertDatabaseHas('push_subscriptions', ['user_id' => $secondUser->id]);
    }

    public function test_user_can_unsubscribe_from_push_notifications(): void
    {
        $user = User::factory()->create();
        $payload = $this->subscriptionPayload();

        $this->actingAs($user)->postJson('/push/subscribe', $payload)->assertOk();

        $this->actingAs($user)
            ->deleteJson('/push/unsubscribe', ['endpoint' => $payload['endpoint']])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_push_public_key_is_exposed_to_the_client(): void
    {
        config()->set('services.webpush.public_key', 'test-vapid-public-key');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/push/public-key')
            ->assertOk()
            ->assertJson(['publicKey' => 'test-vapid-public-key']);
    }

    public function test_system_notification_has_out_of_app_push_payload(): void
    {
        $user = User::factory()->create();
        $notification = new SystemNotification('Faktur baru', 'Ada faktur baru masuk.', '/notifications', 'faktur');

        $this->assertContains(WebPushChannel::class, $notification->via($user));
        $this->assertSame([
            'title' => 'Faktur baru',
            'body' => 'Ada faktur baru masuk.',
            'url' => '/notifications',
            'type' => 'faktur',
            'tag' => 'sales-ledger-'.md5('faktur|Faktur baru|Ada faktur baru masuk.'),
        ], $notification->toWebPush($user));
    }

    public function test_service_worker_handles_push_and_notification_click(): void
    {
        $serviceWorker = file_get_contents(public_path('sw.js'));
        $pushHandler = file_get_contents(public_path('push-sw.js'));

        $this->assertStringContainsString('/push-sw.js', $serviceWorker);
        $this->assertStringContainsString("addEventListener('push'", $pushHandler);
        $this->assertStringContainsString('showNotification', $pushHandler);
        $this->assertStringContainsString("addEventListener('notificationclick'", $pushHandler);
    }

    public function test_profile_and_notification_pages_expose_push_toggle(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('data-push-toggle', false);

        $this->actingAs($admin)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('data-push-toggle', false);
    }
}
