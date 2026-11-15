<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflinePwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_pages_include_pwa_metadata_service_worker_and_install_support(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('rel="manifest" href="/manifest.webmanifest"', false)
            ->assertSee('name="theme-color" content="#4A89F3"', false)
            ->assertSee('name="apple-mobile-web-app-capable" content="yes"', false)
            ->assertSee('rel="apple-touch-icon"', false)
            ->assertSee("navigator.serviceWorker.register('/sw.js'", false)
            ->assertSee('beforeinstallprompt', false)
            ->assertSee('Tambah ke Layar Utama', false);
    }

    public function test_manifest_declares_installable_icons_and_metadata(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertIsArray($manifest);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('id', $manifest['lang']);

        $sizes = array_column($manifest['icons'], 'sizes');
        $purposes = array_column($manifest['icons'], 'purpose');

        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);
        $this->assertContains('maskable', $purposes);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_service_worker_precaches_the_offline_fallback(): void
    {
        $serviceWorker = file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString('offline.html', $serviceWorker);
    }

    public function test_profile_pages_expose_the_install_button(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('data-pwa-install', false);
    }
}
