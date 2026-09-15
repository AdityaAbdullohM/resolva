<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Problem;
use App\Models\ProblemFile;
use App\Models\Submission;
use App\Models\ProblemStageCompletion;
use App\Models\PblValidation;
use App\Models\PblProgress;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProblemController extends Controller
{
    /**
     * Display a listing of the problems for the authenticated guru.
     */
    public function index(Request $request)
    {
        $guruId = auth()->id();
        $kelasIds = auth()->user()->kelasYangDiajar()->pluck('kelas.id');
        
        $query = Problem::whereIn('kelas_id', $kelasIds)
                ->with(['kelas', 'mataPelajaran'])
                ->withCount('submissions');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Get paginated problems for the page
        $problems = $query->latest()->paginate(9)->withQueryString();

        // Load pending stage completions for all problems the teacher teaches (not only current page)
        $allProblemIds = Problem::whereIn('kelas_id', $kelasIds)->pluck('id')->toArray();
        $pendingStages = ProblemStageCompletion::whereIn('problem_id', $allProblemIds)
                    ->where('status', 'pending')
                    ->with(['user', 'problem'])
                    ->get();

        // Pending PBL validation requests created by students (via PblController)
        $pendingPblValidations = PblValidation::where('status', 'pending')
                ->with(['user', 'problem'])
                ->orderBy('created_at')
                ->get();

        return view('guru.problems.index', compact('problems', 'pendingStages', 'pendingPblValidations'));
    }

    /**
     * Teacher validates (approve/reject) a student's stage completion.
     */
    public function validateStage(Request $request, Problem $problem, $stage)
    {
        $user = auth()->user();

        // Ensure teacher is allowed to validate for this problem
        $isTeacher = $problem->kelas && $problem->kelas->teachers->contains($user->id);
        if (!$isTeacher && $user->role !== 'admin') {
            abort(403, 'Anda tidak memiliki izin untuk memvalidasi tahap ini.');
        }

        $stage = intval($stage);
        if ($stage < 1 || $stage > 5) {
            return back()->with('error', 'Tahap tidak valid.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $psc = ProblemStageCompletion::where('problem_id', $problem->id)
                ->where('stage', $stage)
                ->where('status', 'pending')
                ->where('user_id', $request->input('user_id'))
                ->first();

        if (!$psc) {
            return back()->with('error', 'Permintaan validasi tidak ditemukan.');
        }

        $psc->status = $request->input('action') === 'approve' ? 'approved' : 'rejected';
        $psc->teacher_id = $user->id;
        $psc->validated_at = now();
        $psc->save();

        return back()->with('success', 'Status validasi berhasil diperbarui.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kelas $kelas)
    {
        $mataPelajarans = $kelas->mataPelajarans;
        return view('guru.problems.create', compact('kelas', 'mataPelajarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kelas $kelas)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kompetensi_java' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $problem = $kelas->problems()->create(array_merge($validatedData, ['user_id' => Auth::id()]));

        return redirect()->route('dosen.kelas.show', $kelas)->with('success', 'Problem baru berhasil dibuat.');
    }

    /**
     * Show the form for creating a new resource without a specific class.
     */
    public function createGeneral()
    {
        $kelasList = auth()->user()->kelasYangDiajar()->with('mataPelajaran')->get();
        return view('guru.problems.create_general', compact('kelasList'));
    }

    /**
     * Store a newly created resource in storage from the general form.
     */
    public function storeGeneral(Request $request)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kompetensi_java' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'files.*' => 'nullable|file|max:51200', // max 50MB per file
            'links' => 'nullable|array',
            'links.*' => 'nullable|url',
        ]);

        // Check if the authenticated user is actually the teacher for the selected class
        $kelas = Kelas::findOrFail($validatedData['kelas_id']);
        $isTeacherOfClass = $kelas->teachers()->where('users.id', auth()->id())->exists();

        if (!$isTeacherOfClass && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk membuat masalah di kelas ini.');
        }

        $problem = Problem::create(array_merge($validatedData, ['user_id' => Auth::id(), 'links' => $validatedData['links'] ?? null]));

        // Handle uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) continue;
                $path = $file->store('problems/'.$problem->id, 'public');
                ProblemFile::create([
                    'problem_id' => $problem->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('dosen.problems.index')->with('success', 'Problem baru berhasil dibuat.');
    }

    /**
     * Display the specified problem.
     */
    public function show(Kelas $kelas, Problem $problem)
    {
        // Handle possible mismatch when route provides a different kelas id than the problem's relation
        if ($problem->kelas_id !== $kelas->id) {
            $resolvedKelas = $problem->kelas;
            if (! $resolvedKelas) {
                abort(404, 'Problem tidak ditemukan.');
            }
            $kelas = $resolvedKelas;
        }

        // Authorization: ensure the authenticated user teaches the class or is admin
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (! $isTeacher && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke masalah ini.');
        }

        // Determine which PBL stage is being viewed (default to 1)
        $viewStage = intval(request('stage', 1));

        // Prepare students and their PBL status similar to modal
        // Include students who have a submission for this problem OR are members of a group that selected this problem
        $submissionUserIds = Submission::where('problem_id', $problem->id)->pluck('user_id')->unique()->toArray();

        // find group member ids for groups explicitly tied to this problem
        $groupMemberIds = [];
        try {
            $groupsForProblem = Group::where('problem_id', $problem->id)->with('members')->get();
            $groupMemberIds = $groupsForProblem->flatMap(function($g){ return $g->members->pluck('id'); })->unique()->toArray();
        } catch (\Exception $e) {
            $groupMemberIds = [];
        }

        $participantIds = array_values(array_unique(array_merge($submissionUserIds, $groupMemberIds)));

        if (empty($participantIds)) {
            $students = collect();
        } else {
            $students = $kelas->siswa()->whereIn('users.id', $participantIds)->get();
            $students = $students->map(function($s) use ($problem, $viewStage){
                $s->pbl_progress = PblProgress::where('problem_id', $problem->id)->where('user_id', $s->id)->first();
                $s->pending_validations = PblValidation::where('problem_id', $problem->id)->where('user_id', $s->id)->where('status', 'pending')->where('step', $viewStage)->get();
                $s->pending_stage_completions = ProblemStageCompletion::where('problem_id', $problem->id)->where('user_id', $s->id)->where('status', 'pending')->where('stage', $viewStage)->get();
                $s->submission = Submission::where('problem_id', $problem->id)->where('user_id', $s->id)->first();
                return $s;
            });
        }

        return view('guru.problems.show', compact('problem','students'));
    }

    /**
     * Store teacher feedback for a student's submission (optional).
     */
    public function storeStudentFeedback(Request $request, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        // Authorization: ensure the authenticated user teaches the class or is admin
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke masalah ini.');
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'feedback' => 'nullable|string',
        ]);

        $submission = Submission::firstOrNew([
            'problem_id' => $problem->id,
            'user_id' => $data['user_id'],
        ]);

        $submission->feedback = $data['feedback'] ?? null;
        // If it's a new submission and no submitted_at set, leave other fields null
        $submission->save();

        // After saving feedback, create a pending PBL validation for the next step
        try {
            $validatedTo = PblProgress::where('problem_id', $problem->id)->where('user_id', $data['user_id'])->value('validated_to') ?: 0;
            $nextStep = (int) min(5, ($validatedTo + 1));

            $exists = PblValidation::where('problem_id', $problem->id)
                        ->where('user_id', $data['user_id'])
                        ->where('step', $nextStep)
                        ->where('status', 'pending')
                        ->exists();

            if (!$exists) {
                PblValidation::create([
                    'problem_id' => $problem->id,
                    'user_id' => $data['user_id'],
                    'step' => $nextStep,
                    'status' => 'pending',
                ]);
            }
        } catch (\Exception $e) {
            // swallow errors here to avoid breaking feedback flow
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Feedback berhasil disimpan.');
    }

    /**
     * Store rubric scores (4 criteria) and compute final average for a student's submission.
     */
    public function storeStudentGrade(Request $request, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke masalah ini.');
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'c1' => 'required|integer|min:0|max:100',
            'c2' => 'required|integer|min:0|max:100',
            'c3' => 'required|integer|min:0|max:100',
            'c4' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = Submission::firstOrNew([
            'problem_id' => $problem->id,
            'user_id' => $data['user_id'],
        ]);

        $scores = [
            'c1' => (int) $data['c1'],
            'c2' => (int) $data['c2'],
            'c3' => (int) $data['c3'],
            'c4' => (int) $data['c4'],
        ];

        $avg = array_sum($scores) / count($scores);

        // Save rubric scores inside stage_contents[5]['scores'] and final in submission->nilai
        $stageContents = $submission->stage_contents ?? [];
        $stageContents[5] = array_merge($stageContents[5] ?? [], [
            'scores' => $scores,
            'final' => round($avg, 2),
            'graded_by' => auth()->id(),
            'graded_at' => now()->toDateTimeString(),
        ]);

        $submission->stage_contents = $stageContents;
        $submission->nilai = round($avg, 2);
        if (!empty($data['feedback'])) {
            $submission->feedback = $data['feedback'];
        }
        $submission->save();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'final' => $submission->nilai]);
        }

        return back()->with('success', 'Nilai rubrik dan nilai akhir berhasil disimpan.');
    }

    /**
     * Store teacher evaluation (4 criteria) for a student's submission.
     */
    public function storeStudentEvaluation(Request $request, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke masalah ini.');
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'scores' => 'required|array|size:4',
            'scores.*' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = Submission::firstOrNew([
            'problem_id' => $problem->id,
            'user_id' => $data['user_id'],
        ]);

        $scores = array_map('floatval', $data['scores']);
        $avg = count($scores) ? round(array_sum($scores) / count($scores), 2) : null;

        $stageContents = $submission->stage_contents ?? [];
        $stageContents[5] = array_merge($stageContents[5] ?? [], [
            'scores' => $scores,
            'final' => $avg,
            'evaluated_by' => auth()->id(),
            'evaluated_at' => now()->toDateTimeString(),
        ]);

        $submission->stage_contents = $stageContents;
        $submission->nilai = $avg;
        if (!empty($data['feedback'])) $submission->feedback = $data['feedback'];
        $submission->save();

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'avg' => $avg]);
        }

        return back()->with('success', 'Evaluasi berhasil disimpan. Nilai akhir: ' . ($avg !== null ? $avg : '-'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas, Problem $problem)
    {
        $this->authorize('update', $problem);
        // Authorization check: Ensure problem belongs to class
        if ($problem->kelas_id !== $kelas->id) {
            $resolvedKelas = $problem->kelas;
            if (! $resolvedKelas) {
                abort(404, 'Problem tidak ditemukan di kelas ini.');
            }
            $kelas = $resolvedKelas;
        }

        return view('guru.problems.edit', compact('kelas', 'problem'));
    }

    /**
     * Convenience edit route that accepts only a Problem instance and
     * resolves its Kelas. Useful when URLs are generated without the kelas id
     * (prevents 404 in production where links may omit kelas).
     */
    public function editSimple(Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        $this->authorize('update', $problem);

        return view('guru.problems.edit', compact('kelas', 'problem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas, Problem $problem)
    {
        try {
            $this->authorize('update', $problem);
            // Authorization check: Ensure problem belongs to class
            if ($problem->kelas_id !== $kelas->id) {
                $resolvedKelas = $problem->kelas;
                if (! $resolvedKelas) {
                    abort(404, 'Problem tidak ditemukan di kelas ini.');
                }
                $kelas = $resolvedKelas;
            }

        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kompetensi_java' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'files.*' => 'nullable|file|max:51200',
            'links' => 'nullable|array',
            'links.*' => 'nullable|url',
        ]);

        // Update problem basic fields and links
            $problem->update(array_merge($validatedData, ['links' => $validatedData['links'] ?? $problem->links]));

        // Handle new uploaded files (if any)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) continue;
                $path = $file->store('problems/'.$problem->id, 'public');
                ProblemFile::create([
                    'problem_id' => $problem->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

            return redirect()->route('dosen.problems.index')->with('success', 'Problem berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('ProblemController@update: exception', [
                'user_id' => auth()->id(),
                'problem_id' => $problem->id ?? null,
                'kelas_id' => $kelas->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Convenience update that accepts only a Problem and resolves its Kelas,
     * then delegates to the existing update logic.
     */
    public function updateGeneral(Request $request, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (! $kelas) {
            Log::warning('ProblemController@updateGeneral: problem without kelas', ['problem_id' => $problem->id, 'user_id' => auth()->id()]);
            abort(404, 'Problem tidak ditemukan.');
        }
        return $this->update($request, $kelas, $problem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas, Problem $problem)
    {
        $this->authorize('delete', $problem);
        // Authorization check: Ensure problem belongs to class
        if ($problem->kelas_id !== $kelas->id) {
            $resolvedKelas = $problem->kelas;
            if (! $resolvedKelas) {
                abort(404, 'Problem tidak ditemukan di kelas ini.');
            }
            $kelas = $resolvedKelas;
        }

        $problem->delete();

        return redirect()->route('dosen.problems.index')->with('success', 'Problem berhasil dihapus.');
    }

    /**
     * Convenience destroy that accepts only Problem and resolves its Kelas,
     * delegating to the nested destroy implementation.
     */
    public function destroyGeneral(Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        return $this->destroy($kelas, $problem);
    }

    /**
     * Display a listing of submissions for a specific problem.
     */
    public function indexSubmissions(Request $request, Kelas $kelas, Problem $problem)
    {
        $this->authorize('view', $problem);
        // Authorization check: Ensure problem belongs to class
        if ($problem->kelas_id !== $kelas->id) {
            abort(404, 'Problem tidak ditemukan di kelas ini.');
        }

        $query = $problem->submissions()->with('user')->latest();

        // Search by student name or submission content
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($subq) use ($q) {
                $subq->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%");
                })->orWhere('content', 'like', "%{$q}%");
            });
        }

        // Filter by status: graded / ungraded
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'graded') {
                $query->whereNotNull('nilai');
            } elseif ($status === 'ungraded') {
                $query->whereNull('nilai');
            }
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('guru.problems.submissions.index', compact('kelas', 'problem', 'submissions'));
    }

    /**
     * Display the specified submission for grading.
     */
    public function showSubmission(Kelas $kelas, Problem $problem, Submission $submission)
    {
        // Authorization checks
        if (!$kelas->teachers->contains(Auth::user()) && Auth::id() !== $kelas->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }
        if ($problem->kelas_id !== $kelas->id) {
            abort(404, 'Problem tidak ditemukan di kelas ini.');
        }
        if ($submission->problem_id !== $problem->id) {
            abort(404, 'Submission tidak ditemukan untuk problem ini.');
        }

        $submission->load('user'); // Eager load the student's info

        return view('guru.problems.submissions.show', compact('kelas', 'problem', 'submission'));
    }

    /**
     * Grade the specified submission.
     */
    public function gradeSubmission(Request $request, Kelas $kelas, Problem $problem, Submission $submission)
    {
        $this->authorize('update', $problem);
        if ($problem->kelas_id !== $kelas->id) {
            abort(404, 'Problem tidak ditemukan di kelas ini.');
        }
        if ($submission->problem_id !== $problem->id) {
            abort(404, 'Submission tidak ditemukan untuk problem ini.');
        }

        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->nilai = $request->nilai;
        $submission->feedback = $request->feedback;
        $submission->status = 'dinilai'; // Update status to graded
        $submission->save();

        return redirect()->route('dosen.kelas.problems.submissions.index', [$kelas, $problem])->with('success', 'Submission berhasil dinilai.');
    }

    /**
     * Display a listing of problems for a specific class and Mata Kuliah.
     */
    public function indexByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check: Ensure the authenticated user teaches this class and Mata Kuliah
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke masalah ini.');
        }

        $problems = Problem::where('kelas_id', $kelas->id)
                           ->where('mata_pelajaran_id', $mataPelajaran->id) // Assuming problem has mata_pelajaran_id
                           ->with('kelas')
                           ->latest()
                           ->get();

        return view('guru.kelas.mata-pelajaran.problems.index', compact('kelas', 'mataPelajaran', 'problems'));
    }

    /**
     * Return HTML fragment with PBL details for a problem (students, progress, pending validations).
     */
    public function pblDetails(Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404);

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') abort(403);

        $students = $kelas->siswa()->get();

        $students = $students->map(function($s){
            $s->pbl_progress = PblProgress::where('problem_id', request()->route('problem')->id)->where('user_id', $s->id)->first();
            $s->pending_validations = PblValidation::where('problem_id', request()->route('problem')->id)->where('user_id', $s->id)->where('status', 'pending')->where('step', 1)->get();
            $s->pending_stage_completions = ProblemStageCompletion::where('problem_id', request()->route('problem')->id)->where('user_id', $s->id)->where('status', 'pending')->where('stage', 1)->get();
            return $s;
        });

        return view('guru.problems._pbl_modal_content', compact('problem', 'students'));
    }

    /**
     * Show organisasi (grouping) page for a problem.
     */
    public function organisasi(Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) abort(404, 'Problem tidak ditemukan.');

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') abort(403);

        // Include groups that are global (problem_id IS NULL) or specifically for this problem
        $groups = Group::with(['members', 'teacher'])
                    ->where(function($q) use ($problem){
                        $q->whereNull('problem_id')->orWhere('problem_id', $problem->id);
                    })->get();

        // Prepare students and their PBL status for step 2 (Organisasi)
        $students = $kelas->siswa()->get();
        $students = $students->map(function($s) use ($problem){
            $s->pbl_progress = PblProgress::where('problem_id', $problem->id)->where('user_id', $s->id)->first();
            $s->pending_validations = PblValidation::where('problem_id', $problem->id)->where('user_id', $s->id)->where('status', 'pending')->where('step', 2)->get();
            $s->pending_stage_completions = ProblemStageCompletion::where('problem_id', $problem->id)->where('user_id', $s->id)->where('status', 'pending')->where('stage', 2)->get();
            $s->submission = Submission::where('problem_id', $problem->id)->where('user_id', $s->id)->first();
            return $s;
        });

        return view('guru.problems.organisasi', compact('problem', 'kelas', 'groups', 'students'));
    }

    /**
     * Add an instruction to a problem (AJAX)
     */
    public function addInstruction(Request $request, Problem $problem)
    {
        $kelas = $problem->kelas;
        if (!$kelas) return response()->json(['success' => false, 'message' => 'Problem tidak ditemukan.'], 404);

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('users.id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);

        $data = $request->validate([
            'instruction' => 'required|string|max:2000',
            'stage' => 'nullable|integer|min:1|max:5',
        ]);

        $stage = isset($data['stage']) ? (int) $data['stage'] : 2;

        $instructions = $problem->instructions ?: [];
        if (!is_array($instructions)) $instructions = [];

        if (!isset($instructions[$stage]) || !is_array($instructions[$stage])) {
            $instructions[$stage] = [];
        }

        $new = ['text' => $data['instruction'], 'created_by' => auth()->id(), 'created_at' => now()->toDateTimeString()];
        $instructions[$stage][] = $new;
        $problem->instructions = $instructions;
        $problem->save();

        return response()->json(['success' => true, 'instruction' => $new]);
    }

    /**
     * Delete an instruction for a problem (AJAX)
     * URL: DELETE /guru/problems/{problem}/instructions/{stage}/{index}
     */
    public function deleteInstruction(Request $request, Problem $problem, $stage, $index)
    {
        $kelas = $problem->kelas;
        if (!$kelas) return response()->json(['success' => false, 'message' => 'Problem tidak ditemukan.'], 404);

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('user_id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);

        $stage = (int) $stage;
        $index = (int) $index;

        $instructions = $problem->instructions ?: [];
        if (!is_array($instructions)) $instructions = [];

        if (!isset($instructions[$stage]) || !is_array($instructions[$stage]) || !isset($instructions[$stage][$index])) {
            return response()->json(['success' => false, 'message' => 'Instruksi tidak ditemukan.'], 404);
        }

        // remove the instruction
        array_splice($instructions[$stage], $index, 1);
        // if stage array becomes empty, unset it
        if (empty($instructions[$stage])) unset($instructions[$stage]);

        $problem->instructions = $instructions;
        $problem->save();

        return response()->json(['success' => true]);
    }

    /**
     * Update an instruction text for a problem (AJAX)
     * URL: PATCH /guru/problems/{problem}/instructions/{stage}/{index}
     */
    public function updateInstruction(Request $request, Problem $problem, $stage, $index)
    {
        $kelas = $problem->kelas;
        if (!$kelas) return response()->json(['success' => false, 'message' => 'Problem tidak ditemukan.'], 404);

        // Authorization: ensure teacher
        $isTeacher = $kelas->teachers()->where('user_id', auth()->id())->exists();
        if (!$isTeacher && auth()->user()->role !== 'admin') return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);

        $stage = (int) $stage;
        $index = (int) $index;

        $data = $request->validate([
            'instruction' => 'required|string|max:2000',
        ]);

        $instructions = $problem->instructions ?: [];
        if (!is_array($instructions)) $instructions = [];

        if (!isset($instructions[$stage]) || !is_array($instructions[$stage]) || !isset($instructions[$stage][$index])) {
            return response()->json(['success' => false, 'message' => 'Instruksi tidak ditemukan.'], 404);
        }

        // update text and mark edited
        $instructions[$stage][$index] = array_merge((array) $instructions[$stage][$index], [
            'text' => $data['instruction'],
            'edited_by' => auth()->id(),
            'edited_at' => now()->toDateTimeString(),
        ]);

        $problem->instructions = $instructions;
        $problem->save();

        return response()->json(['success' => true]);
    }

    /**
     * Handle AJAX validation actions from guru modal.
     */
    public function pblValidate(Request $request, Problem $problem)
    {
        $this->authorize('update', $problem);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'step' => 'required|integer|min:1|max:5',
            'action' => 'required|in:approve,reject'
        ]);

        $userId = $request->input('user_id');
        $step = intval($request->input('step'));
        $action = $request->input('action');

        // Try to find a pending PblValidation first
        $validation = PblValidation::where('problem_id', $problem->id)
                        ->where('user_id', $userId)
                        ->where('step', $step)
                        ->where('status', 'pending')
                        ->first();

        if ($validation) {
            if ($action === 'reject') {
                $validation->status = 'rejected';
                $validation->validated_by = auth()->id();
                $validation->validated_at = now();
                $validation->save();

                if ($request->wantsJson()) {
                    return response()->json(['success' => true]);
                }

                return redirect()->back()->with('success', 'Permintaan validasi ditolak.');
            }

            // approve
            $validation->status = 'validated';
            $validation->validated_by = auth()->id();
            $validation->validated_at = now();
            $validation->save();

            $progress = PblProgress::firstOrNew([
                'problem_id' => $problem->id,
                'user_id' => $userId,
            ]);
            $progress->validated_to = max($progress->validated_to ?? 0, $step);
            $progress->save();

            // Do not auto-create a pending PblValidation for the next step here.
            // Unlocking/availability is represented by updating PblProgress.validated_to above.

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Permintaan validasi disetujui.');
        }

        // If no PblValidation exists, try to update ProblemStageCompletion
        $psc = ProblemStageCompletion::where('problem_id', $problem->id)
            ->where('user_id', $userId)
            ->where('stage', $step)
            ->where('status', 'pending')
            ->first();

        if ($psc) {
            $psc->status = $action === 'approve' ? 'approved' : 'rejected';
            $psc->teacher_id = auth()->id();
            $psc->validated_at = now();
            $psc->save();

            // If approved, advance PblProgress and create next pending PblValidation
            if ($action === 'approve') {
                $progress = PblProgress::firstOrNew([
                    'problem_id' => $problem->id,
                    'user_id' => $userId,
                ]);
                $progress->validated_to = max($progress->validated_to ?? 0, $step);
                $progress->save();

                // Next step availability for the student is driven by PblProgress.validated_to.
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Status validasi berhasil diperbarui.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada permintaan yang ditemukan.'], 404);
        }

        return redirect()->back()->with('error', 'Tidak ada permintaan yang ditemukan.');
    }

    /**
     * Show the form for creating a new problem for a specific class and Mata Kuliah.
     */
    public function createByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk membuat masalah di sini.');
        }

        return view('guru.kelas.mata-pelajaran.problems.create', compact('kelas', 'mataPelajaran'));
    }

    /**
     * Store a newly created problem for a specific class and Mata Kuliah.
     */
    public function storeByKelasAndMataPelajaran(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan masalah di sini.');
        }

        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kompetensi_java' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
        ]);

        $problem = new Problem();
        $problem->judul = $validatedData['judul'];
        $problem->deskripsi = $validatedData['deskripsi'];
        $problem->kompetensi_java = $validatedData['kompetensi_java'];
        $problem->deadline = $validatedData['deadline'];
        $problem->kelas_id = $kelas->id;
        $problem->mata_pelajaran_id = $mataPelajaran->id; // Associate with Mata Kuliah
        $problem->user_id = Auth::id(); // Assign the authenticated user's ID
        $problem->save();

        return redirect()->route('dosen.kelas.mata-kuliah.problems.index', [$kelas, $mataPelajaran])->with('success', 'Problem baru berhasil dibuat.');
    }

    /**
     * Display a listing of submissions for problems related to a specific class and Mata Kuliah.
     */
    public function indexSubmissionsByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check: Ensure the authenticated user teaches this class and Mata Kuliah
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke submissions ini.');
        }

        $problemIds = Problem::where('kelas_id', $kelas->id)
                             ->where('mata_pelajaran_id', $mataPelajaran->id)
                             ->pluck('id');

        $submissions = Submission::whereIn('problem_id', $problemIds)
                                 ->with(['problem.kelas', 'user'])
                                 ->latest()
                                 ->get();

        return view('guru.kelas.mata-pelajaran.submissions.index', compact('kelas', 'mataPelajaran', 'submissions'));
    }
}
