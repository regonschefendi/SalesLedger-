<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Faktur extends Model
{
    protected $fillable = [
        'toko_id',
        'nomor_faktur',
        'tanggal_nota',
        'tanggal_pembayaran',
        'metode_bayar',
        'total_tagihan',
        'catatan',
        'total_dibayar',
        'status',
        'foto_url',
        'foto_path',
        'sales_id',
    ];

    protected static function booted(): void
    {
        // Hapus file di Supabase saat faktur dihapus lewat aplikasi/Eloquent.
        static::deleted(function (Faktur $faktur) {
            $faktur->deleteFotoFromStorage();
        });
    }

    /**
     * Ambil path file di disk Supabase. Data lama hanya punya foto_url,
     * jadi path diturunkan dari URL publiknya.
     */
    public static function resolveFotoPath(?string $fotoPath, ?string $fotoUrl): ?string
    {
        if (filled($fotoPath)) {
            return ltrim($fotoPath, '/');
        }

        if (blank($fotoUrl)) {
            return null;
        }

        $marker = '/storage/v1/object/public/';
        $position = strpos($fotoUrl, $marker);

        if ($position === false) {
            return null;
        }

        // Format URL: .../public/<bucket>/<path>. Buang segmen bucket.
        $segments = explode('/', substr($fotoUrl, $position + strlen($marker)), 2);

        if (count($segments) < 2 || $segments[1] === '') {
            return null;
        }

        return ltrim(rawurldecode($segments[1]), '/');
    }

    public function fotoStoragePath(): ?string
    {
        return static::resolveFotoPath($this->foto_path, $this->foto_url);
    }

    public function deleteFotoFromStorage(): void
    {
        $path = $this->fotoStoragePath();

        if (! $path) {
            return;
        }

        // Jangan hapus kalau masih ada faktur lain yang memakai file yang sama.
        $masihDipakai = static::query()
            ->where('id', '!=', $this->getKey())
            ->where(function (Builder $query) use ($path) {
                $query->where('foto_path', $path);

                if ($this->foto_url) {
                    $query->orWhere('foto_url', $this->foto_url);
                }
            })
            ->exists();

        if ($masihDipakai) {
            return;
        }

        try {
            Storage::disk('supabase')->delete($path);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus foto faktur dari Supabase.', [
                'faktur_id' => $this->getKey(),
                'foto_path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sales_id', 'id');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function scopeForAdmin(Builder $query, User|int $admin): Builder
    {
        $adminId = $admin instanceof User ? $admin->id : $admin;

        return $query->whereIn(
            'sales_id',
            User::where('parent_admin_id', $adminId)->select('id')
        );
    }

    public function scopeForSales(Builder $query, User|int $sales): Builder
    {
        return $query->where('sales_id', $sales instanceof User ? $sales->id : $sales);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->role === 'admin'
            ? $query->forAdmin($user)
            : $query->forSales($user);
    }
}
