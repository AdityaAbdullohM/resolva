<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\TahunAjaran;
use App\Models\Semester;

use App\Models\Discussion;
use App\Models\Quiz;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade

class DashboardController extends Controller
{
    public function dashboard()
    {
        $guru = Auth::user();
        $kelasDiajar = $guru->kelasYangDiajar()
            ->with([
                'mataPelajaran' => function ($query) use ($guru) {
                    $query->where('kelas_mata_pelajaran.user_id', $guru->id);
                },
                'tahunAjaran',
                'semester',
                'siswa'
            ])
            ->get()
            ->unique('id');

        $kelasCount = $kelasDiajar->count();

        // Get subject IDs taught by the guru
        $mataPelajaranIds = $kelasDiajar->flatMap(function ($kelas) {
            return $kelas->mataPelajaran->pluck('id');
        })->unique();

        // Calculate total unique students
        $studentIds = collect();
        foreach ($kelasDiajar as $kelas) {
            $studentIds = $studentIds->merge($kelas->siswa->pluck('id'));
        }
        $studentsCount = $studentIds->unique()->count();

        // Get all problem IDs for the classes taught by the guru
        $problemIds = Problem::whereIn('kelas_id', $kelasDiajar->pluck('id'))->pluck('id');
        $problemsCount = $problemIds->count();
        
        // Ringkasan Aktivitas Mengajar
        $materisCount = Materi::whereIn('mata_pelajaran_id', $mataPelajaranIds)->count();
        $activeProblemsCount = Problem::whereIn('kelas_id', $kelasDiajar->pluck('id'))->where('deadline', '>=', now())->count();
        $tugasBelumDinilaiCount = Submission::whereIn('problem_id', $problemIds)->whereNull('nilai')->count();

        // Notifikasi Penting
        $newSubmissionsCount = Submission::whereIn('problem_id', $problemIds)->where('created_at', '>=', now()->subDays(3))->count();
        $approachingDeadlinesCount = Problem::whereIn('kelas_id', $kelasDiajar->pluck('id'))->where('deadline', '>', now())->where('deadline', '<=', now()->addDays(3))->count();
        $discussionIds = Discussion::whereIn('problem_id', $problemIds)->pluck('id');
        $newDiscussionPostsCount = Discussion::whereIn('id', $discussionIds)->whereHas('posts', function ($query) {
            $query->where('created_at', '>=', now()->subDays(3));
        })->count();

        // Statistik Ringan
        $submissionsCount = Submission::whereIn('problem_id', $problemIds)->count();
        $totalPossibleSubmissions = $studentsCount * $problemsCount;
        $submissionPercentage = $totalPossibleSubmissions > 0 ? round(($submissionsCount / $totalPossibleSubmissions) * 100) : 0;

        // Daftar Tugas Perlu Tindakan
        $tugasPerluTindakan = Problem::whereIn('kelas_id', $kelasDiajar->pluck('id'))
            ->whereHas('submissions', function ($query) {
                $query->whereNull('nilai');
            })
            ->withCount(['submissions as submissions_to_grade_count' => function ($query) {
                $query->whereNull('nilai');
            }])
            ->take(5)
            ->get();


        return view('guru.dashboard', compact(
            'guru',
            'kelasDiajar',
            'materisCount',
            'activeProblemsCount',
            'tugasBelumDinilaiCount',
            'newSubmissionsCount',
            'approachingDeadlinesCount',
            'newDiscussionPostsCount',
            'studentsCount',
            'submissionPercentage',
            'tugasPerluTindakan'
        ));
    }
}
