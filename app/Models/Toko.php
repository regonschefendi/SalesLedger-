<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $fillable = [
        'nama_toko',
        'no_telp',
        'alamat',
        'provinsi',
        'kota',
        'kode_pos',
        'sales_id'
    ];

    public function fakturs()
    {
        return $this->hasMany(Faktur::class);
    }
}
