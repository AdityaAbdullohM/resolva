<?php

namespace App\Policies;

use App\Models\Refleksi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RefleksiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSiswa();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Refleksi $refleksi): bool
    {
        return $user->id === $refleksi->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSiswa();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Refleksi $refleksi): bool
    {
        return $user->id === $refleksi->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Refleksi $refleksi): bool
    {
        return $user->id === $refleksi->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Refleksi $refleksi): bool
    {
        return $user->id === $refleksi->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Refleksi $refleksi): bool
    {
        return $user->id === $refleksi->user_id;
    }
}
