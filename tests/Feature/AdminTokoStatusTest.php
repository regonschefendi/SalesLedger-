<?php

namespace Tests\Feature;

use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTokoStatusTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $code = 'ADMIN1'): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'admin_code' => $code,
        ]);
    }

    private function makeSales(User $admin): User
    {
        return User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $admin->id,
            'admin_code' => null,
        ]);
    }

    private function makeToko(User $admin, string $nama): Toko
    {
        return Toko::create([
            'admin_id' => $admin->id,
            'nama_toko' => $nama,
        ]);
    }

    private function makeFaktur(Toko $toko, User $sales, float $totalTagihan, float $totalDibayar): Faktur
    {
        return Faktur::create([
            'toko_id' => $toko->id,
            'sales_id' => $sales->id,
            'nomor_faktur' => 'INV-'.uniqid(),
            'tanggal_nota' => now()->toDateString(),
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'status' => $totalDibayar >= $totalTagihan ? 'lunas' : 'belum_lunas',
        ]);
    }

    public function test_store_without_invoices_is_not_marked_as_paid(): void
    {
        $admin = $this->makeAdmin();
        $this->makeSales($admin);
        $toko = $this->makeToko($admin, 'Toko Tanpa Faktur');

        $this->actingAs($admin)
            ->get('/admin/toko')
            ->assertOk()
            ->assertViewHas('tokos', function ($tokos) use ($toko) {
                $status = $tokos->firstWhere('id', $toko->id)->status_pembayaran;

                return $status === 'belum_ada_faktur';
            })
            ->assertViewHas('stats', fn ($stats) => $stats['belum_lunas'] === 0);
    }

    public function test_store_with_fully_paid_invoices_is_marked_as_paid(): void
    {
        $admin = $this->makeAdmin();
        $sales = $this->makeSales($admin);
        $toko = $this->makeToko($admin, 'Toko Lunas');
        $this->makeFaktur($toko, $sales, 100000, 100000);

        $this->actingAs($admin)
            ->get('/admin/toko')
            ->assertOk()
            ->assertViewHas('tokos', function ($tokos) use ($toko) {
                return $tokos->firstWhere('id', $toko->id)->status_pembayaran === 'lunas';
            });
    }

    public function test_store_with_unpaid_invoices_is_marked_as_unpaid(): void
    {
        $admin = $this->makeAdmin();
        $sales = $this->makeSales($admin);
        $toko = $this->makeToko($admin, 'Toko Belum Lunas');
        $this->makeFaktur($toko, $sales, 100000, 50000);

        $this->actingAs($admin)
            ->get('/admin/toko')
            ->assertOk()
            ->assertViewHas('tokos', function ($tokos) use ($toko) {
                return $tokos->firstWhere('id', $toko->id)->status_pembayaran === 'belum_lunas';
            })
            ->assertViewHas('stats', fn ($stats) => $stats['belum_lunas'] === 1);
    }
}
