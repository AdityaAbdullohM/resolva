<?php

namespace App\Policies;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class ProblemPolicy
{
    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return bool|void
     */
    public function before(User $user, string $ability)
    {
        if ($user->isAdmin()) { // Assuming you have an isAdmin() method on your User model
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Problem  $problem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (! $kelas) {
            return Response::deny('Problem tidak terkait dengan kelas.');
        }

        // Use a DB-level exists check rather than Collection->contains to avoid problems when relations are not loaded
        $isTeacher = $kelas->teachers()->where('users.id', $user->id)->exists();
        if ($isTeacher || $user->id === $kelas->user_id) {
            return Response::allow();
        }

        return Response::deny('Anda tidak memiliki akses untuk melihat tugas ini.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isGuru() // Assuming you have an isGuru() method on your User model
                ? Response::allow()
                : Response::deny('Anda tidak memiliki akses untuk membuat tugas.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Problem  $problem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (! $kelas) {
            return Response::deny('Problem tidak terkait dengan kelas.');
        }

        $isTeacher = $kelas->teachers()->where('users.id', $user->id)->exists();
        if ($isTeacher || $user->id === $kelas->user_id) {
            return Response::allow();
        }

        return Response::deny('Anda tidak memiliki akses untuk mengubah tugas ini.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Problem  $problem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (! $kelas) {
            return Response::deny('Problem tidak terkait dengan kelas.');
        }

        $isTeacher = $kelas->teachers()->where('users.id', $user->id)->exists();
        if ($isTeacher || $user->id === $kelas->user_id) {
            return Response::allow();
        }

        return Response::deny('Anda tidak memiliki akses untuk menghapus tugas ini.');
    }
}