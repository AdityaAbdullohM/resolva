<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaKelompokController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all groups the user is a member of
        $myGroups = $user->groups()->with(['kelas', 'members'])->get();

        if ($myGroups->isNotEmpty()) {
            // If user is in at least one group, show only those groups
            $groups = $myGroups;
        } else {
            // If user is not in any group, show all available groups from their classes
            $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
            $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
            $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

            if (empty($kelasIds)) {
                $groups = collect();
            } else {
                $problemIds = \App\Models\Problem::whereIn('kelas_id', $kelasIds)->pluck('id');
                $groups = Group::whereIn('problem_id', $problemIds)
                                ->with(['kelas', 'members'])
                                ->get();
            }
        }

        return view('siswa.kelompok.index', compact('groups'));
    }

    /**
     * Join the specified group as the authenticated student. A student can only join one group.
     */
    public function join(Group $kelompok)
    {
        $user = Auth::user();

        // A student can only join one group at a time.
        if ($user->groups()->exists()) {
            return redirect()->route('mahasiswa.kelompok.index')->with('error', 'Anda sudah tergabung di sebuah kelompok. Anda hanya dapat bergabung dengan satu kelompok.');
        }

        $kelompok->members()->attach($user->id);

        return redirect()->route('mahasiswa.kelompok.index')->with('success', 'Berhasil bergabung ke kelompok.');
    }

    public function show(Group $kelompok)
    {
        $user = Auth::user();
        if (!$kelompok->members->contains($user)) {
            abort(403);
        }

        $kelompok->load('members', 'kelas');
        return view('siswa.kelompok.show', compact('kelompok'));
    }
}