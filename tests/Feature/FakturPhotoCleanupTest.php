<?php

namespace Tests\Feature;

use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FakturPhotoCleanupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('supabase');
    }

    private function createFaktur(array $attributes = []): Faktur
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $sales = User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $admin->id,
        ]);
        $toko = Toko::create([
            'admin_id' => $admin->id,
            'nama_toko' => 'Toko Uji',
        ]);

        return Faktur::create(array_merge([
            'toko_id' => $toko->id,
            'nomor_faktur' => 'INV-TEST',
            'tanggal_nota' => now()->toDateString(),
            'total_tagihan' => 100000,
            'total_dibayar' => 0,
            'status' => 'belum_lunas',
            'sales_id' => $sales->id,
            'foto_url' => 'https://example.test/storage/v1/object/public/fakturs/foto.jpg',
            'foto_path' => 'foto.jpg',
        ], $attributes));
    }

    public function test_deleting_a_faktur_removes_its_photo_from_storage(): void
    {
        Storage::disk('supabase')->put('foto.jpg', 'image-bytes');

        $faktur = $this->createFaktur();

        $faktur->delete();

        Storage::disk('supabase')->assertMissing('foto.jpg');
    }

    public function test_legacy_faktur_without_foto_path_can_still_delete_its_photo(): void
    {
        Storage::disk('supabase')->put('legacy.jpg', 'image-bytes');

        $faktur = $this->createFaktur([
            'foto_path' => null,
            'foto_url' => 'https://example.test/storage/v1/object/public/fakturs/legacy.jpg',
        ]);

        $this->assertSame('legacy.jpg', $faktur->fotoStoragePath());

        $faktur->delete();

        Storage::disk('supabase')->assertMissing('legacy.jpg');
    }

    public function test_photo_is_kept_when_another_faktur_still_uses_it(): void
    {
        Storage::disk('supabase')->put('shared.jpg', 'image-bytes');

        $first = $this->createFaktur([
            'nomor_faktur' => 'INV-1',
            'foto_path' => 'shared.jpg',
            'foto_url' => 'https://example.test/storage/v1/object/public/fakturs/shared.jpg',
        ]);

        $second = $this->createFaktur([
            'nomor_faktur' => 'INV-2',
            'foto_path' => 'shared.jpg',
            'foto_url' => 'https://example.test/storage/v1/object/public/fakturs/shared.jpg',
        ]);

        $first->delete();
        Storage::disk('supabase')->assertExists('shared.jpg');

        $second->delete();
        Storage::disk('supabase')->assertMissing('shared.jpg');
    }

    public function test_purge_command_removes_orphaned_photos_and_keeps_used_ones(): void
    {
        Storage::disk('supabase')->put('used.jpg', 'image-bytes');
        Storage::disk('supabase')->put('orphan.jpg', 'image-bytes');

        $this->createFaktur([
            'foto_path' => 'used.jpg',
            'foto_url' => 'https://example.test/storage/v1/object/public/fakturs/used.jpg',
        ]);

        $this->artisan('faktur:purge-orphans', ['--older-than' => 0])
            ->assertExitCode(0);

        Storage::disk('supabase')->assertExists('used.jpg');
        Storage::disk('supabase')->assertMissing('orphan.jpg');
    }
}
