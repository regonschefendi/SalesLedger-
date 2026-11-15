<?php

namespace Tests\Feature;

use Tests\TestCase;

class OfflinePwaTest extends TestCase
{
    public function test_auth_pages_include_the_pwa_manifest_and_service_worker_registration(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('rel="manifest" href="/manifest.webmanifest"', false)
            ->assertSee("navigator.serviceWorker.register('/sw.js'", false);
    }

    public function test_offline_fallback_is_precached_and_the_manifest_is_valid(): void
    {
        $serviceWorker = file_get_contents(public_path('sw.js'));
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertStringContainsString('offline.html', $serviceWorker);
        $this->assertIsArray($manifest);
        $this->assertSame('/', $manifest['start_url']);
    }
}
