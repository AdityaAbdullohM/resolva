<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use App\Models\ProblemStageCompletion;
use Illuminate\Http\Request;
use App\Services\JdoodleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaProblemController extends Controller
{
    /**
     * Display a listing of the problems for the authenticated student.
     */
    public function index()
    {
        $user = Auth::user();

        // Collect class IDs the student belongs to. Students may be linked to a class
        // either via the pivot table `kelas_user` (kelasYangDiikuti) or via the
        // `users.kelas_id` column. Include both sources to avoid missing problems.
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];

        $kelasIds = collect($pivotKelasIds)
                    ->merge($directKelasId)
                    ->unique()
                    ->values()
                    ->all();

        // If no kelas found, return empty collection to the view
        if (empty($kelasIds)) {
            $problems = collect();
            return view('siswa.problems.index', compact('problems'));
        }

        // Fetch problems belonging to those kelas IDs and eager load related data
        $problems = Problem::whereIn('kelas_id', $kelasIds)
                    ->with(['kelas.mataPelajaran'])
                    ->get()
                    ->sortBy(function ($problem) {
                        return $problem->deadline ?? $problem->created_at;
                    })->values();

        // Load stage completions for the current user for these problems
        $stageCompletions = ProblemStageCompletion::whereIn('problem_id', $problems->pluck('id')->toArray())
                                ->where('user_id', $user->id)
                                ->get()
                                ->groupBy('problem_id');

        // Determine groups for the current user scoped to these problems (if any)
        $userGroupMap = [];
        $problemIds = $problems->pluck('id')->toArray();
        $userGroups = Auth::user()->groups()->where(function($q) use ($problemIds){
            $q->whereNull('problem_id')->orWhereIn('problem_id', $problemIds);
        })->get();
        foreach ($userGroups as $g) {
            // map group to its problem_id (null -> use key 'global')
            $key = $g->problem_id ?? 'global';
            $userGroupMap[$key] = $g->id;
        }

        return view('siswa.problems.index', compact('problems', 'stageCompletions', 'userGroupMap'));
    }

    /**
     * Student marks a stage as completed (requests validation by teacher).
     */
    public function completeStage(Request $request, Problem $problem, $stage)
    {
        $user = Auth::user();

        // Validate stage value
        $stage = intval($stage);
        if ($stage < 1 || $stage > 5) {
            return back()->with('error', 'Tahap tidak valid.');
        }

        // Ensure student belongs to the class
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        // Create or update the stage completion as pending
        $psc = ProblemStageCompletion::updateOrCreate([
            'problem_id' => $problem->id,
            'user_id' => $user->id,
            'stage' => $stage,
        ], [
            'status' => 'pending',
            'teacher_id' => null,
            'validated_at' => null,
        ]);

        return back()->with('success', 'Permintaan validasi untuk tahap dikirim ke guru.');
    }

    /**
     * Display the specified problem for the authenticated student.
     */
    public function show(Problem $problem)
    {
        $user = Auth::user();

        // Eager load the class and its mataPelajaran for the problem
        $problem->load(['kelas.mataPelajaran']);

        // Authorization check: Ensure the student is enrolled in the class this problem belongs to.
        // A student can belong to a class either via the pivot `kelas_user` or via the
        // `users.kelas_id` column, so combine both sources when checking membership.
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        $submission = $problem->submissions()->where('user_id', $user->id)->first();

        return view('siswa.problems.show', compact('problem', 'submission'));
    }

    /**
     * Store quick feedback / short answer (content-only) from the problem show page.
     * Creates or updates a Submission record without file upload.
     */
    public function storeFeedback(Request $request, Problem $problem)
    {
        $user = Auth::user();

        // Authorization: ensure student belongs to kelas
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        $request->validate([
            'content' => 'nullable|string|max:2000',
        ]);

        $submission = $problem->submissions()->where('user_id', $user->id)->first();

        if (! $submission) {
            $submission = new Submission();
            $submission->problem_id = $problem->id;
            $submission->user_id = $user->id;
        }

        // Store quick feedback per-stage in `stage_contents` JSON column. This keeps
        // feedback for different PBL stages isolated (e.g., stage 2 separate from stage 1).
        $stage = intval($request->input('stage', 5));
        if ($stage < 1 || $stage > 5) $stage = 5;

        $content = $request->input('content');

        $stageContents = $submission->stage_contents ?? [];
        $stageContents[$stage] = [
            'text' => $content,
            'updated_at' => now()->toDateTimeString(),
        ];

        $submission->stage_contents = $stageContents;
        $submission->submitted_at = now();

        if ($problem->deadline && now()->isAfter($problem->deadline)) {
            $submission->status = 'late';
        } else {
            $submission->status = 'submitted';
        }

        $submission->save();

        // If a stage was provided (via query or form), mark that PBL stage as pending
        try {
            ProblemStageCompletion::updateOrCreate([
                'problem_id' => $problem->id,
                'user_id' => $user->id,
                'stage' => $stage,
            ], [
                'status' => 'pending',
                'teacher_id' => null,
                'validated_at' => null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark PBL stage pending after feedback: ' . $e->getMessage());
        }

        return back()->with('success', 'Feedback berhasil dikirim.');
    }

    /**
     * Store student's self-evaluation rubric for stage 5 (4 criteria).
     */
    public function storeRubric(Request $request, Problem $problem)
    {
        $user = Auth::user();

        // Authorization: ensure student belongs to kelas
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        $data = $request->validate([
            'c1' => 'nullable|numeric|min:0|max:100',
            'c2' => 'nullable|numeric|min:0|max:100',
            'c3' => 'nullable|numeric|min:0|max:100',
            'c4' => 'nullable|numeric|min:0|max:100',
        ]);

        $submission = $problem->submissions()->where('user_id', $user->id)->first();
        if (! $submission) {
            $submission = new Submission();
            $submission->problem_id = $problem->id;
            $submission->user_id = $user->id;
            $submission->submitted_at = now();
        }

        $scores = [
            'c1' => isset($data['c1']) ? floatval($data['c1']) : null,
            'c2' => isset($data['c2']) ? floatval($data['c2']) : null,
            'c3' => isset($data['c3']) ? floatval($data['c3']) : null,
            'c4' => isset($data['c4']) ? floatval($data['c4']) : null,
        ];

        $valid = array_filter($scores, function($v){ return $v !== null && $v !== ''; });
        $avg = count($valid) ? round(array_sum($valid) / count($valid), 2) : null;

        $stageContents = $submission->stage_contents ?? [];
        $stageContents[5] = array_merge($stageContents[5] ?? [], [
            'self_scores' => $scores,
            'self_final' => $avg,
            'self_updated_at' => now()->toDateTimeString(),
        ]);

        $submission->stage_contents = $stageContents;
        $submission->save();

        return back()->with('success', 'Self-evaluasi berhasil disimpan.');
    }

    /**
     * Show the form for creating a new submission for the specified problem.
     */
    public function createSubmission(Problem $problem)
    {
        $user = Auth::user();

        // Authorization check: Ensure the student is enrolled in the class this problem belongs to
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        // Deadline check removed to allow late submissions

        // If a submission exists, load it and pass to the view so student can view/edit it.
        $submission = $problem->submissions()->where('user_id', $user->id)->first();

        return view('siswa.problems.submit', compact('problem', 'submission'));
    }

    /**
     * Store a newly created submission in storage.
     */
    public function storeSubmission(Request $request, Problem $problem)
    {
        $user = Auth::user();

        // Authorization check: Ensure the student is enrolled in the class this problem belongs to
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        // Deadline check removed to allow late submissions

        // Allow stage-specific file uploads (stage=2,3,4) even if a submission already exists.
        $stage = intval($request->input('stage', 0));

        // Validation: for normal full submission (no stage) files are optional; for stage uploads, files are required only for stage 2-4
        $rules = [
            'content' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|max:10240', // Max 10MB per file
        ];

        $messages = [
            'files.required' => 'File tugas wajib diunggah.',
            'files.*.required' => 'Setiap file harus valid.',
        ];

        $request->validate($rules, $messages);

        $submission = $problem->submissions()->where('user_id', $user->id)->first();

        // If this is a stage-specific upload for stage 1, 2, 3, or 4, handle it first.
        if (in_array($stage, [1,2,3,4], true)) {
            if (! $submission) {
                $submission = new Submission();
                $submission->problem_id = $problem->id;
                $submission->user_id = $user->id;
                $submission->submitted_at = now();
                if ($problem->deadline && now()->isAfter($problem->deadline)) {
                    $submission->status = 'late';
                } else {
                    $submission->status = 'submitted';
                }
            }

            $stageContents = $submission->stage_contents ?? [];
            $stored = $stageContents[$stage]['files'] ?? [];

            // Store files if provided (now optional)
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $f) {
                    if (!$f->isValid()) continue;
                    $orig = $f->getClientOriginalName();
                    $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $orig);
                    $filename = time() . '_' . uniqid() . '_' . $safe;
                    $stored[] = $f->storeAs('public/submissions/' . $problem->id . '/stage' . $stage . '/' . $user->id, $filename);
                }
            }

            // Update stage contents with text and files
            $stageContents[$stage] = array_merge($stageContents[$stage] ?? [], [
                'text' => $request->content,
                'files' => $stored,
                'updated_at' => now()->toDateTimeString(),
            ]);

            $submission->stage_contents = $stageContents;
            $submission->submitted_at = now();
            $submission->save();

            // Mark stage as pending validation
            try {
                ProblemStageCompletion::updateOrCreate([
                    'problem_id' => $problem->id,
                    'user_id' => $user->id,
                    'stage' => $stage,
                ], [
                    'status' => 'pending',
                    'teacher_id' => null,
                    'validated_at' => null,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to mark PBL stage pending after stage ' . $stage . ' upload: ' . $e->getMessage());
            }

            return redirect()->route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $stage])->with('success', 'Jawaban untuk Stage ' . $stage . ' berhasil disimpan.');
        }

        // If no submission exists, create a new main submission record
        if (! $submission) {
            $submission = new Submission();
            $submission->problem_id = $problem->id;
            $submission->user_id = $user->id;
            $submission->content = $request->content;
            $submission->submitted_at = now();
            if ($problem->deadline && now()->isAfter($problem->deadline)) {
                $submission->status = 'late';
            } else {
                $submission->status = 'submitted';
            }

            // Store main files if provided (full submission)
            $paths = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $f) {
                    if (!$f->isValid()) continue;
                    $orig = $f->getClientOriginalName();
                    $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $orig);
                    $filename = time() . '_' . uniqid() . '_' . $safe;
                    $paths[] = $f->storeAs('public/submissions/' . $problem->id . '/' . $user->id, $filename);
                }
            }

            if (!empty($paths)) {
                $submission->file_path = $paths;
            }

            $submission->save();

            return redirect()->route('mahasiswa.problems.submit.edit', $problem)->with('success', 'Solusi berhasil dikumpulkan!');
        }

        // If submission exists and no stage-specific handling, block further full submissions
        return redirect()->route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $stage])->with('error', 'Anda sudah mengumpulkan solusi untuk problem ini.');
    }

    /**
     * Show the form for editing an existing submission.
     */
    public function editSubmission(Problem $problem)
    {
        $user = Auth::user();

        // Authorization check: Ensure the student is enrolled in the class this problem belongs to
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        $submission = $problem->submissions()->where('user_id', $user->id)->firstOrFail();

        // Deadline check removed to allow late edits

        // Check if submission is already graded
        if ($submission->status === 'dinilai') {
            return redirect()->route('mahasiswa.problems.show', $problem)->with('error', 'Solusi sudah dinilai dan tidak dapat diubah lagi.');
        }

        return view('siswa.problems.submit', compact('problem', 'submission'));
    }

    /**
     * Update an existing submission in storage.
     */
    public function updateSubmission(Request $request, Problem $problem)
    {
        $user = Auth::user();

        // Authorization check: Ensure the student is enrolled in the class this problem belongs to
        $pivotKelasIds = $user->kelasYangDiikuti->pluck('id')->toArray();
        $directKelasId = $user->kelas_id ? [$user->kelas_id] : [];
        $kelasIds = collect($pivotKelasIds)->merge($directKelasId)->unique()->values()->all();

        if (!in_array($problem->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak terdaftar di kelas tempat problem ini berada.');
        }

        $submission = $problem->submissions()->where('user_id', $user->id)->firstOrFail();

        // Deadline check removed to allow late edits

        // Check if submission is already graded
        if ($submission->status === 'dinilai') {
            return redirect()->route('mahasiswa.problems.show', $problem)->with('error', 'Solusi sudah dinilai dan tidak dapat diubah lagi.');
        }

        $stage = intval($request->input('stage', 0));

        $request->validate([
            'content' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        $submission->content = $request->content;
        $submission->submitted_at = now(); // Update submission time
        
        if ($problem->deadline && now()->isAfter($problem->deadline)) {
            $submission->status = 'late';
        } else {
            $submission->status = 'submitted';
        }

        // If updating stage 3 or 4 specifically, store files under stage_contents[stage] and do not delete main files
        if (in_array($stage, [3,4], true) && $request->hasFile('files')) {
            $stageContents = $submission->stage_contents ?? [];
            $stored = $stageContents[$stage]['files'] ?? [];

            foreach ($request->file('files') as $f) {
                if (!$f->isValid()) continue;
                $orig = $f->getClientOriginalName();
                $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $orig);
                $filename = time() . '_' . uniqid() . '_' . $safe;
                $stored[] = $f->storeAs('public/submissions/' . $problem->id . '/stage' . $stage . '/' . $user->id, $filename);
            }

            $stageContents[$stage] = array_merge($stageContents[$stage] ?? [], [
                'files' => $stored,
                'updated_at' => now()->toDateTimeString(),
            ]);

            $submission->stage_contents = $stageContents;

            // Mark stage as pending validation
            try {
                ProblemStageCompletion::updateOrCreate([
                    'problem_id' => $problem->id,
                    'user_id' => $user->id,
                    'stage' => $stage,
                ], [
                    'status' => 'pending',
                    'teacher_id' => null,
                    'validated_at' => null,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to mark PBL stage pending after stage ' . $stage . ' update: ' . $e->getMessage());
            }
        } elseif ($request->hasFile('files')) {
            // Existing behavior for replacing main submission files
            if ($submission->file_path && is_array($submission->file_path)) {
                foreach ($submission->file_path as $oldPath) {
                    Storage::delete($oldPath);
                }
            } elseif ($submission->file_path && is_string($submission->file_path)) {
                Storage::delete($submission->file_path);
            }

            $newPaths = [];
            foreach ($request->file('files') as $f) {
                if (!$f->isValid()) continue;
                $orig = $f->getClientOriginalName();
                $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $orig);
                $filename = time() . '_' . uniqid() . '_' . $safe;
                $newPaths[] = $f->storeAs('public/submissions/' . $problem->id . '/' . $user->id, $filename);
            }

            if (!empty($newPaths)) {
                $submission->file_path = $newPaths;
            }
        }

        $submission->save();

        return redirect()->route('mahasiswa.problems.submit.edit', $problem)->with('success', 'Solusi berhasil diperbarui!');
    }

    /**
     * Show/download the file for a specific submission.
     */
    public function showSubmissionFile(Submission $submission)
    {
        $user = Auth::user();

        // Eager load relationships for efficiency
        $submission->load('problem.kelas.teachers');

        // Authorization check
        // Allow if the user is the owner of the submission
        $isOwner = $user->id === $submission->user_id;

        // Allow if the user is a teacher for the class associated with the submission
        $isTeacher = false;
        if ($submission->problem && $submission->problem->kelas) {
            $isTeacher = $submission->problem->kelas->teachers->contains($user->id);
        }

        // Allow if user is admin
        $isAdmin = $user->role === 'admin';

        if (!$isOwner && !$isTeacher && !$isAdmin) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses file ini.');
        }

        // Support files stored in main `file_path` or per-stage `stage_contents[stage]['files']`.
        $stage = intval(request()->query('stage', 0));

        if ($stage >= 1) {
            $stageContents = $submission->stage_contents ?? [];
            $files = data_get($stageContents, $stage . '.files', null);
            if (!is_array($files)) {
                abort(404, 'File tidak ditemukan.');
            }
            $index = intval(request()->query('index', 0));
            if (!isset($files[$index]) || !Storage::exists($files[$index])) {
                abort(404, 'File tidak ditemukan.');
            }
            $full = Storage::path($files[$index]);
            $name = basename($files[$index]);
            return response()->download($full, $name);
        }

        // Handle multiple file paths stored as array in main file_path
        $filePath = $submission->file_path;
        if (is_array($filePath)) {
            $index = intval(request()->query('index', 0));
            if (!isset($filePath[$index]) || !Storage::exists($filePath[$index])) {
                abort(404, 'File tidak ditemukan.');
            }
            $full = Storage::path($filePath[$index]);
            $name = basename($filePath[$index]);
            return response()->download($full, $name);
        }

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $full = Storage::path($filePath);
        $name = is_string($filePath) ? basename($filePath) : 'file';
        return response()->download($full, $name);
    }


    /**
     * Display a listing of submissions made by the authenticated student.
     */
    public function indexSubmissions()
    {
        $user = Auth::user();

        $submissions = Submission::where('user_id', $user->id)
                                ->with(['problem.kelas']) // Eager load problem and its class
                                ->latest()
                                ->get();

        return view('siswa.nilai.index', compact('submissions'));
    }

    

    /**
     * Show the form for student enrollment.
     */
    public function createEnrollment()
    {
        return view('siswa.enroll.create');
    }

    /**
     * Store the student's enrollment in a class.
     */
    public function storeEnrollment(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|string|exists:kelas,kode_kelas',
        ]);

        $kelas = Kelas::where('kode_kelas', $request->kode_kelas)->first();
        $user = Auth::user();

        // Check if already enrolled
        if ($user->kelasYangDiikuti->contains($kelas->id)) {
            return back()->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        $user->kelasYangDiikuti()->attach($kelas->id);

        return redirect()->route('mahasiswa.problems.index')->with('success', 'Berhasil bergabung ke kelas ' . $kelas->nama . '!');
    }

    /**
     * Show the Java compiler interface.
     */
    public function compiler()
    {
        return view('siswa.compiler.index');
    }

    /**
     * Extracts the public class name from a string of Java code.
     *
     * @param string $code
     * @return string|null
     */
    private function extractClassName(string $code): ?string
    {
        if (preg_match('/public\s+class\s+([a-zA-Z_][a-zA-Z0-9_]*)/', $code, $matches)) {
            return $matches[1];
        }
        return null;
    }
    /**
     * Compile and run Java code with instrumentation.
     */
    public function runCode(Request $request)
    {
        $tempDir = null;
        try {
            $request->validate([
                'code' => 'required|string',
            ]);

            $user = Auth::user();
            $code = $request->input('code');

            // Prefer using JDoodle API when credentials are present. JDoodle avoids
            // executing arbitrary code on the server and works across environments.
            $jdClientId = env('JDOODLE_CLIENT_ID');
            $jdClientSecret = env('JDOODLE_CLIENT_SECRET');

            if ($jdClientId && $jdClientSecret) {
                $versionIndex = $request->input('versionIndex', '4');
                $jdoodle = app()->make(JdoodleService::class);
                $res = $jdoodle->execute($code, 'java', $versionIndex);

                if (isset($res['error']) && $res['error'] === true) {
                    return response()->json(['status' => 'error', 'output' => $res['body'] ?? 'JDoodle request failed.'], 500);
                }

                // JDoodle returns an 'output' key containing stdout/stderr combined.
                $output = $res['output'] ?? ($res['result'] ?? ($res['output_html'] ?? ''));
                return response()->json(['status' => 'success', 'output' => $output]);
            }

            // Fallback to local javac/java execution (existing behavior)
            $className = $this->extractClassName($code);
            if (!$className) {
                return response()->json(['status' => 'error', 'output' => "Error: Tidak dapat menemukan deklarasi 'public class' di dalam kode Anda."]);
            }

            $tempDir = 'temp/' . $user->id . '/' . uniqid();
            Storage::makeDirectory($tempDir);
            $absoluteTempDir = Storage::path($tempDir);

            $javaFileName = $className . '.java';
            $javaFilePath = $absoluteTempDir . DIRECTORY_SEPARATOR . $javaFileName;
            file_put_contents($javaFilePath, $code);

            // Get the path to the Java compiler from the environment file.
            $javacPath = env('JAVA_COMPILER_PATH');
            if (!$javacPath) {
                return response()->json(['status' => 'error', 'output' => "Error: Jalur kompiler Java (JAVA_COMPILER_PATH) tidak diatur di file .env Anda. Pastikan Anda telah menginstal JDK dan mengatur path ke direktori 'bin'-nya."], 500);
            }

            // Construct the full command for javac and java
            $javacCmd = '"' . rtrim($javacPath, '\\/') . DIRECTORY_SEPARATOR . 'javac' . '"';
            $javaCmd = '"' . rtrim($javacPath, '\\/') . DIRECTORY_SEPARATOR . 'java' . '"';

            $compileOutput = [];
            $returnVar = -1;
            exec("{$javacCmd} \"{$javaFilePath}\" 2>&1", $compileOutput, $returnVar);

            if ($returnVar !== 0) {
                return response()->json(['status' => 'error', 'output' => implode("\n", $compileOutput)]);
            }

            $runOutput = [];
            exec("{$javaCmd} -cp \"{$absoluteTempDir}\" {$className} 2>&1", $runOutput, $returnVar);

            return response()->json(['status' => 'success', 'output' => implode("\n", $runOutput)]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Java code execution failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'output' => 'Terjadi kesalahan tak terduga di server. Silakan coba lagi nanti.'], 500);
        } finally {
            if ($tempDir && Storage::exists($tempDir)) {
                Storage::deleteDirectory($tempDir);
            }
        }
    }

    // Tracing functionality removed.
}
