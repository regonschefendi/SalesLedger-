<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OcrScanTest extends TestCase
{
    use RefreshDatabase;

    private function salesUser(): User
    {
        return User::factory()->create([
            'role' => 'sales',
        ]);
    }

    private function geminiResponse(array $data): array
    {
        return $this->geminiRawResponse(json_encode($data));
    }

    private function geminiRawResponse(string $text): array
    {
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $text],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_it_extracts_invoice_data_from_a_scanned_photo(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'Nama Toko' => 'Toko Sumber Rejeki',
                'Nomor Faktur' => 'INV-001',
                'Total Tagihan' => 'Rp 1.500.000',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama_toko' => 'Toko Sumber Rejeki',
                    'nomor_faktur' => 'INV-001',
                    'total_tagihan' => '1500000',
                ],
            ]);
    }

    public function test_it_extracts_and_normalizes_the_invoice_date(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'Nama Toko' => 'Toko Sumber Rejeki',
                'Nomor Faktur' => 'INV-001',
                'Tanggal Nota' => '24 November 2026',
                'Total Tagihan' => '1500000',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()
            ->assertJsonPath('data.tanggal_nota', '24/11/2026');
    }

    public function test_it_accepts_iso_dates_from_the_ai(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'Nama Toko' => 'Toko Baru',
                'tanggal_nota' => '2026-11-24',
                'total_tagihan' => '250000',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()
            ->assertJsonPath('data.tanggal_nota', '24/11/2026');
    }

    public function test_it_leaves_the_date_empty_when_the_ai_returns_an_invalid_date(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'Nama Toko' => 'Toko Baru',
                'Tanggal Nota' => 'kemarin sore',
                'Total Tagihan' => '250000',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()
            ->assertJsonPath('data.tanggal_nota', '');
    }

    public function test_it_generates_a_fallback_invoice_number_when_ai_does_not_return_one(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'Nama Toko' => 'Toko Baru',
                'Total Tagihan' => '250000',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()
            ->assertJsonPath('data.nama_toko', 'Toko Baru')
            ->assertJsonPath('data.total_tagihan', '250000');

        $this->assertStringStartsWith('INV-', $response->json('data.nomor_faktur'));
    }

    public function test_it_rejects_non_image_uploads(): void
    {
        config()->set('services.gemini.key', 'test-key');

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->create('nota.txt', 10, 'text/plain'),
            ]);

        $response->assertUnprocessable();
    }

    public function test_it_returns_a_service_error_when_gemini_is_unavailable(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'busy'], 503),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertStatus(502)
            ->assertJson(['success' => false]);
    }

    public function test_it_returns_a_clear_error_when_gemini_key_is_missing(): void
    {
        config()->set('services.gemini.key', null);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertStatus(503)
            ->assertJson(['success' => false]);
    }

    public function test_scan_requires_an_authenticated_sales_user(): void
    {
        $response = $this->postJson(route('sales.scan'), [
            'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
        ]);

        $response->assertUnauthorized()
            ->assertJson(['success' => false]);
    }

    public function test_home_page_uses_the_in_app_camera_before_the_native_camera(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_code' => 'ADMIN1',
        ]);

        $sales = User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $admin->id,
            'admin_code' => null,
        ]);

        $this->actingAs($sales)
            ->get(route('sales.home'))
            ->assertOk()
            ->assertSee('camera-modal', false)
            ->assertSee('handleCameraLabelClick', false)
            ->assertSee('getUserMedia', false);
    }

    public function test_it_normalizes_indonesian_and_international_thousand_separators(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'nama_toko' => 'Toko Sumber Rejeki',
                'total_tagihan' => '1.500.000,00',
            ])),
        ]);

        $response = $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ]);

        $response->assertOk()->assertJsonPath('data.total_tagihan', '1500000');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'nama_toko' => 'Toko Sumber Rejeki',
                'total_tagihan' => 'Rp 1,500,000.00',
            ])),
        ]);

        $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ])
            ->assertOk()
            ->assertJsonPath('data.total_tagihan', '1500000');
    }

    public function test_it_parses_json_even_when_the_ai_adds_extra_text(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiRawResponse(
                'Berikut hasil pembacaan faktur: {"nama_toko":"Toko Baru","nomor_faktur":"INV-9","total_tagihan":"250000"} Semoga membantu.'
            )),
        ]);

        $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ])
            ->assertOk()
            ->assertJsonPath('data.nama_toko', 'Toko Baru')
            ->assertJsonPath('data.nomor_faktur', 'INV-9')
            ->assertJsonPath('data.total_tagihan', '250000');
    }

    public function test_it_normalizes_iso_datetimes_and_month_first_dates(): void
    {
        config()->set('services.gemini.key', 'test-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'nama_toko' => 'Toko Baru',
                'tanggal_nota' => '2026-11-24T10:30:00',
                'total_tagihan' => '250000',
            ])),
        ]);

        $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ])
            ->assertOk()
            ->assertJsonPath('data.tanggal_nota', '24/11/2026');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($this->geminiResponse([
                'nama_toko' => 'Toko Baru',
                'tanggal_nota' => 'November 24, 2026',
                'total_tagihan' => '250000',
            ])),
        ]);

        $this->actingAs($this->salesUser())
            ->postJson(route('sales.scan'), [
                'image' => UploadedFile::fake()->image('nota.jpg', 120, 160),
            ])
            ->assertOk()
            ->assertJsonPath('data.tanggal_nota', '24/11/2026');
    }

    public function test_sales_home_issues_a_remember_cookie_so_camera_app_switches_do_not_log_the_user_out(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_code' => 'ADMIN1',
        ]);

        $sales = User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $admin->id,
            'admin_code' => null,
        ]);

        $response = $this->actingAs($sales)->get(route('sales.home'));

        $response->assertOk();

        $rememberCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_'));

        $this->assertNotNull($rememberCookie);
        $this->assertNotNull($sales->fresh()->remember_token);
    }
}
