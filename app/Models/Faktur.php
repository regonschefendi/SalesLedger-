<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faktur extends Model
{
    protected $fillable = [
        'nama_toko',
        'nomor_faktur',
        'tanggal_nota',
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
}
