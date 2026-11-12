<?php

namespace App\Models;

use App\Auth\CachedUserProvider;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

#[Fillable(['full_name', 'username', 'email', 'password', 'phone_number', 'role', 'admin_code', 'parent_admin_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (User $user) => Cache::forget(CachedUserProvider::cacheKey($user->getKey())));
        static::deleted(fn (User $user) => Cache::forget(CachedUserProvider::cacheKey($user->getKey())));
    }

    // Admin memiliki banyak Sales
    public function sales()
    {
        return $this->hasMany(User::class, 'parent_admin_id', 'id');
    }

    // Sales terhubung ke satu Admin
    public function parentAdmin()
    {
        return $this->belongsTo(User::class, 'parent_admin_id', 'id');
    }

    public function salesIds()
    {
        return $this->sales()->pluck('id');
    }

    public function tokosPegangan()
    {
        return $this->belongsToMany(Toko::class, 'sales_tokos', 'user_id', 'toko_id');
    }

    public function fakturs()
    {
        return $this->hasMany(Faktur::class, 'sales_id');
    }
}
