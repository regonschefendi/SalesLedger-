<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class CachedUserProvider extends EloquentUserProvider
{
    public static function cacheKey(mixed $identifier): string
    {
        return 'auth:user:'.$identifier;
    }

    /**
     * Ambil user dari cache dulu supaya request yang sudah login tidak
     * selalu menembak database yang latensinya tinggi.
     */
    public function retrieveById($identifier)
    {
        $key = static::cacheKey($identifier);
        $cached = Cache::get($key);

        if ($cached instanceof Authenticatable) {
            return $cached;
        }

        $user = parent::retrieveById($identifier);

        if ($user) {
            $this->cacheUser($user);
        }

        return $user;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        if ($user) {
            $this->cacheUser($user);
        }

        return $user;
    }

    protected function cacheUser(Authenticatable $user): void
    {
        Cache::put(
            static::cacheKey($user->getAuthIdentifier()),
            $user,
            now()->addSeconds((int) config('auth.user_cache_ttl', 300))
        );
    }
}
