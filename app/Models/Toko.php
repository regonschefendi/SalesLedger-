<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'admin_id',
        'nama_toko',
        'no_telp',
        'alamat',
        'provinsi',
        'kota',
        'kode_pos',
        'sales_id',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function fakturs()
    {
        return $this->hasMany(Faktur::class);
    }

    public function sales()
    {
        return $this->belongsToMany(User::class, 'sales_tokos', 'toko_id', 'user_id');
    }

    public function scopeOwnedByAdmin(Builder $query, User|int $admin): Builder
    {
        return $query->where('admin_id', $admin instanceof User ? $admin->id : $admin);
    }
}
