<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{

    use SoftDeletes;

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

    public function sales()
    {
        return $this->belongsToMany(User::class, 'sales_tokos', 'toko_id', 'user_id');
    }
}
