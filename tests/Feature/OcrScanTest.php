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
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode($data)],
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
}
