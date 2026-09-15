<?php

namespace App\Http\Controllers\Siswa;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengumuman;
use App\Models\Problem;
use App\Models\Discussion;
use App\Models\KelasMataPelajaran;
use App\Models\Quiz;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        $siswa = Auth::user();
        $kelas = $siswa->kelas()->first();

        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $semester = Semester::where('status', 'aktif')->first();

        // If the student is not in any active class, we can return early or handle it gracefully
        if (!$kelas) {
            // You might want to redirect them to a page that tells them they are not enrolled in any class
            return view('siswa.dashboard', [
                'stats' => ['active_pbl' => 0],
                'mataPelajarans' => [],
                'tugasTerbaru' => [],
                'pengumumanTerbaru' => [],
                'tahunAjaran' => $tahunAjaran,
                'semester' => $semester,
                'kelas' => null
            ]);
        }

        $kelasIds = [$kelas->id];

        // Get all Mata Kuliah IDs from the classes the student is enrolled in
        $mataPelajaranIds = \App\Models\KelasMataPelajaran::whereIn('kelas_id', $kelasIds)
            ->pluck('mata_pelajaran_id')
            ->unique();

        // Now, get the MataPelajaran models for display
        $mataPelajarans = MataPelajaran::whereIn('id', $mataPelajaranIds)
            ->withCount(['problems', 'materis'])
            ->get();

        // Get teachers for each subject
        $teacherMap = [];
        $pivots = \App\Models\KelasMataPelajaran::whereIn('kelas_id', $kelasIds)->with('guru')->get();
        foreach ($pivots as $pivot) {
            if (!isset($teacherMap[$pivot->mata_pelajaran_id])) {
                $teacherMap[$pivot->mata_pelajaran_id] = $pivot->guru;
            }
        }

        // Attach teacher to each subject
        foreach ($mataPelajarans as $mapel) {
            $mapel->teacher = $teacherMap[$mapel->id] ?? null;
        }

        $problemsCount = Problem::whereIn('kelas_id', $kelasIds)->count();
        $submissionsCount = $siswa->submissions()->count();
        $refleksiCount = $siswa->refleksi()->count();
        $pengumumanCount = Pengumuman::whereIn('mata_pelajaran_id', $mataPelajaranIds)->where('published_at', '<=', now())->count();
        $discussionCount = $siswa->discussions()->count();
        $quizzesCount = Quiz::whereIn('mata_pelajaran_id', $mataPelajaranIds)->count();

        // Calculate average grade (nilai) as: (sum of submitted assignment scores + sum of quiz scores)
        // divided by (count of submitted assignments + count of quiz attempts with score)
        $submissionQuery = $siswa->submissions()
            ->whereNotNull('nilai')
            ->whereRaw("`nilai` REGEXP '^[0-9]+(\\\.[0-9]*)?$'"); // ensure numeric

        $sumSubmissions = (float) $submissionQuery->sum('nilai');
        $countSubmissions = (int) $submissionQuery->count();

        $quizAttemptsQuery = QuizAttempt::where('user_id', $siswa->id)
            ->whereNotNull('score')
            ->whereHas('quiz', function ($q) use ($mataPelajaranIds) {
                $q->whereIn('mata_pelajaran_id', $mataPelajaranIds);
            });

        $sumQuizScores = (float) $quizAttemptsQuery->sum('score');
        $countQuizAttempts = (int) $quizAttemptsQuery->count();

        $totalCount = $countSubmissions + $countQuizAttempts;
        $averageGrade = $totalCount > 0 ? ($sumSubmissions + $sumQuizScores) / $totalCount : null;

        $problemIdsInClass = Problem::whereIn('kelas_id', $kelasIds)->pluck('id')->toArray();
        $eligibleProblemIds = $siswa->groups()
            ->whereNotNull('problem_id')
            ->whereIn('problem_id', $problemIdsInClass)
            ->pluck('problem_id')
            ->unique()
            ->values()
            ->all();

        $stats = [
            'kelas' => 1,
            'problems' => $problemsCount,
            'active_pbl' => count($eligibleProblemIds),
            'submissions' => $submissionsCount,
            'refleksi' => $refleksiCount,
            'pengumuman' => $pengumumanCount,
            'discussions' => $discussionCount,
            'quizzes' => $quizzesCount,
            'average_grade' => $averageGrade !== null ? (float) $averageGrade : null,
        ];

        $tugasTerbaru = Problem::whereIn('id', $eligibleProblemIds)
            ->with(['mataPelajaran', 'submissions' => function ($query) use ($siswa) {
                $query->where('user_id', $siswa->id);
            }])
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        $pengumumanTerbaru = Pengumuman::where(function ($query) use ($mataPelajaranIds) {
            $query->whereIn('mata_pelajaran_id', $mataPelajaranIds)
                  ->orWhereNull('mata_pelajaran_id');
        })
            ->where('published_at', '<=', now())
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact('stats', 'mataPelajarans', 'tugasTerbaru', 'pengumumanTerbaru', 'tahunAjaran', 'semester', 'kelas'));
    }
}
