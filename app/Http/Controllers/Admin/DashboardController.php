<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = Kelas::count();
        $totalMataPelajaran = MataPelajaran::count();
        $totalProblems = Problem::count();
        $totalPengumuman = \App\Models\Pengumuman::count();
        $totalMateris = Materi::count();
        $totalDiscussions = \App\Models\Discussion::count();
        $totalQuizzes = \App\Models\Quiz::count();
        $recentUsers = User::latest()->take(5)->get();

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        \Log::info('Active Tahun Ajaran: ' . ($activeTahunAjaran ? $activeTahunAjaran->nama : 'None'));
        $activeSemester = Semester::where('status', 'aktif')->first();
        \Log::info('Active Semester: ' . ($activeSemester ? $activeSemester->nama : 'None'));

        return view('admin.dashboard', compact('totalSiswa', 'totalGuru', 'totalKelas', 'totalMataPelajaran', 'totalProblems', 'totalPengumuman', 'totalMateris', 'totalDiscussions', 'totalQuizzes', 'recentUsers', 'activeTahunAjaran', 'activeSemester'));
    }
}
