<?php

namespace Tests\Feature;

use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateFakturInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSalesWithAdmin(): array
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

        $toko = Toko::create([
            'admin_id' => $admin->id,
            'nama_toko' => 'Toko Sumber Rejeki',
        ]);

        return [$admin, $sales, $toko];
    }

    private function fakturPayload(Toko $toko): array
    {
        return [
            'toko_id' => $toko->id,
            'nomor_faktur' => 'INV-001',
            'tanggal_nota' => '15/11/2026',
            'total_tagihan' => 100000,
            'total_dibayar' => 25000,
            'metode_bayar' => 'Cash',
            'foto_file' => UploadedFile::fake()->image('nota.jpg', 120, 160),
        ];
    }

    public function test_saving_an_invoice_returns_links_to_view_and_download_it(): void
    {
        Storage::fake('supabase');
        Notification::fake();
        config()->set('services.supabase.url', 'https://example.supabase.co');
        config()->set('services.supabase.bucket', 'fakturs');

        [, $sales, $toko] = $this->makeSalesWithAdmin();

        $response = $this->actingAs($sales)->post(
            route('sales.addFaktur'),
            $this->fakturPayload($toko),
            ['Accept' => 'application/json']
        );

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['faktur_id', 'invoice_url', 'invoice_download_url']);

        $faktur = Faktur::firstOrFail();

        $this->assertSame($faktur->id, $response->json('faktur_id'));

        $this->actingAs($sales)
            ->get($response->json('invoice_url'))
            ->assertOk()
            ->assertSee('INV-001');

        $this->actingAs($sales)
            ->get($response->json('invoice_download_url'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_the_success_screen_exposes_view_and_download_invoice_actions(): void
    {
        [, $sales] = $this->makeSalesWithAdmin();

        $this->actingAs($sales)
            ->get(route('sales.input'))
            ->assertOk()
            ->assertSee('success-view-invoice', false)
            ->assertSee('success-download-invoice', false)
            ->assertSee('Lihat Invoice')
            ->assertSee('Unduh Invoice');
    }

    public function test_another_sales_cannot_view_the_generated_invoice(): void
    {
        Storage::fake('supabase');
        Notification::fake();
        config()->set('services.supabase.url', 'https://example.supabase.co');
        config()->set('services.supabase.bucket', 'fakturs');

        [, $sales, $toko] = $this->makeSalesWithAdmin();

        $response = $this->actingAs($sales)->post(
            route('sales.addFaktur'),
            $this->fakturPayload($toko),
            ['Accept' => 'application/json']
        );

        $otherSales = User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $sales->parent_admin_id,
            'admin_code' => null,
        ]);

        $this->actingAs($otherSales)
            ->get($response->json('invoice_url'))
            ->assertNotFound();
    }
}
