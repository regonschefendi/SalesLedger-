<?php

namespace App\Policies;

use App\Models\Toko;
use App\Models\User;

class TokoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'sales'], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Toko $toko): bool
    {
        if ($user->role === 'admin') {
            return $toko->admin_id === $user->id;
        }

        return $user->role === 'sales'
            && $user->parent_admin_id !== null
            && $toko->admin_id === $user->parent_admin_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Toko $toko): bool
    {
        return $user->role === 'admin' && $toko->admin_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Toko $toko): bool
    {
        return $user->role === 'admin' && $toko->admin_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Toko $toko): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Toko $toko): bool
    {
        return false;
    }
}
