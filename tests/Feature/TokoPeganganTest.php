<?php

namespace Tests\Feature;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TokoPeganganTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $code = 'ADMIN1'): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'admin_code' => $code,
        ]);
    }

    private function makeSales(?User $admin): User
    {
        return User::factory()->create([
            'role' => 'sales',
            'parent_admin_id' => $admin?->id,
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

    public function test_sales_only_sees_stores_owned_by_their_admin(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin('ADMIN2');
        $sales = $this->makeSales($admin);

        $mine = $this->makeToko($admin, 'Toko Saya');
        $foreign = $this->makeToko($otherAdmin, 'Toko Asing');

        $sales->tokosPegangan()->attach([$mine->id, $foreign->id]);

        $this->actingAs($sales)
            ->get('/sales/toko-pegangan')
            ->assertOk()
            ->assertViewHas('assignedTokos', fn ($tokos) => $tokos->pluck('id')->all() === [$mine->id])
            ->assertViewHas('allTokos', fn ($tokos) => $tokos->pluck('id')->all() === [$mine->id]);
    }

    public function test_sales_can_sync_store_assignments(): void
    {
        $admin = $this->makeAdmin();
        $sales = $this->makeSales($admin);

        $oldToko = $this->makeToko($admin, 'Toko Lama');
        $newToko = $this->makeToko($admin, 'Toko Baru');

        $sales->tokosPegangan()->attach($oldToko->id);

        $this->actingAs($sales)
            ->post('/sales/toko-pegangan', ['toko_ids' => [$newToko->id]])
            ->assertRedirect('/sales/toko-pegangan')
            ->assertSessionHas('success');

        $this->assertSame([$newToko->id], $sales->fresh()->tokosPegangan->pluck('id')->all());
    }

    public function test_sales_can_remove_all_store_assignments(): void
    {
        $admin = $this->makeAdmin();
        $sales = $this->makeSales($admin);
        $toko = $this->makeToko($admin, 'Toko Saya');

        $sales->tokosPegangan()->attach($toko->id);

        $this->actingAs($sales)
            ->post('/sales/toko-pegangan', [])
            ->assertRedirect('/sales/toko-pegangan');

        $this->assertCount(0, $sales->fresh()->tokosPegangan);
    }

    public function test_sales_cannot_assign_store_owned_by_another_admin(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin('ADMIN2');
        $sales = $this->makeSales($admin);
        $foreign = $this->makeToko($otherAdmin, 'Toko Asing');

        $this->actingAs($sales)
            ->post('/sales/toko-pegangan', ['toko_ids' => [$foreign->id]])
            ->assertSessionHasErrors(['toko_ids.0']);

        $this->assertCount(0, $sales->fresh()->tokosPegangan);
    }

    public function test_sales_cannot_assign_a_soft_deleted_store(): void
    {
        $admin = $this->makeAdmin();
        $sales = $this->makeSales($admin);
        $toko = $this->makeToko($admin, 'Toko Dihapus');
        $toko->delete();

        $this->actingAs($sales)
            ->post('/sales/toko-pegangan', ['toko_ids' => [$toko->id]])
            ->assertSessionHasErrors(['toko_ids.0']);
    }

    public function test_sales_without_admin_is_redirected_to_role_selection(): void
    {
        $sales = $this->makeSales(null);

        $this->actingAs($sales)
            ->get('/sales/toko-pegangan')
            ->assertRedirect('/select-role');
    }

    public function test_store_search_is_limited_to_the_sales_admin(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Pencarian toko memakai ILIKE yang khusus PostgreSQL.');
        }

        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin('ADMIN2');
        $sales = $this->makeSales($admin);

        $this->makeToko($admin, 'Toko Saya');
        $this->makeToko($otherAdmin, 'Toko Asing');

        $this->actingAs($sales)
            ->getJson('/sales/toko/search?q=Toko')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['nama_toko' => 'Toko Saya'])
            ->assertJsonMissing(['nama_toko' => 'Toko Asing']);
    }

    public function test_store_search_returns_nothing_without_an_admin_connection(): void
    {
        $sales = $this->makeSales(null);

        $this->actingAs($sales)
            ->getJson('/sales/toko/search?q=Toko')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_sales_cannot_save_an_invoice_for_a_store_owned_by_another_admin(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin('ADMIN2');
        $sales = $this->makeSales($admin);
        $foreign = $this->makeToko($otherAdmin, 'Toko Asing');

        $this->actingAs($sales)
            ->postJson('/sales/faktur/simpan', [
                'toko_id' => $foreign->id,
                'nomor_faktur' => 'INV-001',
                'tanggal_nota' => '01/01/2026',
                'total_tagihan' => 1000,
                'total_dibayar' => 0,
                'metode_bayar' => 'Cash',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['toko_id']);
    }
}
