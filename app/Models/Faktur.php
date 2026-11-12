<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        'sales_id',
    ];

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
