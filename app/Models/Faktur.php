<?php

namespace App\Models;

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
        'sales_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'sales_id', 'id');
    }

    public function toko() {
        return $this->belongsTo(Toko::class);
    }
}
