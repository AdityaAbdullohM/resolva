<?php

namespace App\Policies;

use App\Models\Materi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MateriPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Materi $materi): bool
    {
        // Allow all authenticated users to view materi details.
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isGuru() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Materi $materi): bool
    {
        return $user->isTeacherOrAdminForKelas($materi->kelas);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Materi $materi): bool
    {
        return $user->isTeacherOrAdminForKelas($materi->kelas);
    }
}
