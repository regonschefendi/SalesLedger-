<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faktur extends Model
{
    protected $fillable = ['nama_toko', 'tanggal_nota', 'total_tagihan', 'nominal_tagihan', 'catatan'];

}
