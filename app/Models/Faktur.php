<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faktur extends Model
{
    protected $fillable = [
        'nama_toko',
        'nomor_faktur',
        'tanggal_nota',
        'total_tagihan',
        'catatan',
        'total_dibayar',
        'status',
        'sales_id'
    ];
}
