    <?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\KelasController;
    use App\Http\Controllers\MateriController;
    use App\Http\Controllers\ProblemController;
    use App\Http\Controllers\SiswaProblemController;
    use App\Http\Controllers\Admin\AdminUserController;
    use App\Http\Controllers\Guru\DiscussionController;
    use App\Http\Controllers\Guru\QuizController;
    use App\Http\Controllers\Guru\GroupController;
    use App\Http\Controllers\Guru\PenilaianController;
    use App\Http\Controllers\Siswa\SiswaMataPelajaranController;
    use App\Http\Controllers\Siswa\SiswaKelasController;
    use App\Http\Controllers\Siswa\QuizController as SiswaQuizController;
    use App\Http\Controllers\Siswa\SiswaKelompokController;
    use App\Http\Controllers\Siswa\SiswaPenilaianController;
    use App\Models\Kelas;
    use App\Models\Materi;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Siswa\PblController;
    use App\Http\Controllers\Guru\PblValidationController;

    Route::get('/', function () {
        return view('welcome');
    });

    // Model binding for Kelas
    Route::bind('kelas', function ($value, $route) {
        \Illuminate\Support\Facades\Log::info('Route binding kelas', [
            'value' => $value,
            'route_name' => $route->getName(),
        ]);

        $kelas = Kelas::find($value);
        
        if ($kelas) {
            \Illuminate\Support\Facades\Log::info('Route binding: kelas found by id', [
                'kelas_id' => $kelas->id,
            ]);
            return $kelas;
        }
        
        \Illuminate\Support\Facades\Log::error('Route binding: kelas not found', [
            'kelas_id' => $value,
        ]);
        abort(404, "Kelas $value tidak ditemukan");
    });

    // Ensure nested materi routes resolve a Materi within the requested kelas.
    Route::bind('materi', function ($value, $route) {
        \Illuminate\Support\Facades\Log::info('Route binding materi', [
            'value' => $value,
            'route_name' => $route->getName(),
            'route_uri' => $route->uri(),
        ]);

        $kelas = $route->parameter('kelas');
        $kelasId = null;

        // If kelas is a Kelas model instance
        if ($kelas instanceof Kelas) {
            $kelasId = $kelas->id;
            \Illuminate\Support\Facades\Log::info('Route binding: kelas is Kelas instance', [
                'kelas_id' => $kelasId,
                'materi_id' => $value,
            ]);
        }
        // If kelas is a string or int (ID)
        elseif ($kelas && (is_string($kelas) || is_numeric($kelas))) {
            $kelasId = (int) $kelas;
            \Illuminate\Support\Facades\Log::info('Route binding: kelas is string/int ID', [
                'kelas_id' => $kelasId,
                'materi_id' => $value,
            ]);
        }

        // If we have a kelas ID, do strict check
        if ($kelasId) {
            \Illuminate\Support\Facades\Log::info('Route binding: doing strict kelas check', [
                'kelas_id' => $kelasId,
                'materi_id' => $value,
            ]);
            
            $materi = Materi::where('id', $value)
                ->where('kelas_id', $kelasId)
                ->first();
            
            if ($materi) {
                \Illuminate\Support\Facades\Log::info('Route binding: materi found with kelas check', [
                    'materi_id' => $materi->id,
                    'kelas_id' => $kelasId,
                ]);
                return $materi;
            }
            
            \Illuminate\Support\Facades\Log::error('Route binding: materi not found with kelas check', [
                'materi_id' => $value,
                'kelas_id' => $kelasId,
            ]);
            abort(404, "Materi $value tidak ditemukan di kelas $kelasId");
        }

        // No kelas parameter, find materi by ID only
        \Illuminate\Support\Facades\Log::info('Route binding: no kelas parameter, finding materi by id only', [
            'materi_id' => $value,
        ]);
        
        $materi = Materi::find($value);
        
        if ($materi) {
            \Illuminate\Support\Facades\Log::info('Route binding: materi found by id', [
                'materi_id' => $materi->id,
            ]);
            return $materi;
        }
        
        \Illuminate\Support\Facades\Log::error('Route binding: materi not found', [
            'materi_id' => $value,
        ]);
        abort(404, "Materi $value tidak ditemukan");
    });

    // Backward compatibility: redirect old URLs to the new terminology
    Route::get('/siswa/{path?}', function ($path = null) {
        $target = $path ? '/mahasiswa/' . $path : '/mahasiswa';
        return redirect()->to($target, 301);
    })->where('path', '.*');

    Route::get('/guru/{path?}', function ($path = null) {
        $target = $path ? '/dosen/' . $path : '/dosen';
        return redirect()->to($target, 301);
    })->where('path', '.*');

    // Legacy named route aliases for backward compatibility with older guru views/components
    Route::get('/guru/kelas/{kelas}', function ($kelas) {
        return redirect()->route('dosen.kelas.show', $kelas);
    })->name('guru.kelas.show');

    Route::get('/guru/kuis', function () {
        return redirect()->route('dosen.kuis.index');
    })->name('guru.kuis.index');

    Route::get('/guru/kuis/create', function () {
        return redirect()->route('dosen.kuis.create');
    })->name('guru.kuis.create');

    Route::get('/guru/kuis/{kuis}', function ($kuis) {
        return redirect()->route('dosen.kuis.show', $kuis);
    })->name('guru.kuis.show');

    Route::get('/guru/kuis/{kuis}/edit', function ($kuis) {
        return redirect()->route('dosen.kuis.edit', $kuis);
    })->name('guru.kuis.edit');

    Route::get('/guru/kuis/{kuis}/questions/create', function ($kuis) {
        return redirect()->route('dosen.kuis.questions.create', ['kuis' => $kuis]);
    })->name('guru.kuis.questions.create');

    Route::post('/guru/kuis', function () {
        return redirect()->route('dosen.kuis.store');
    })->name('guru.kuis.store');

    Route::put('/guru/kuis/{kuis}', function ($kuis) {
        return redirect()->route('dosen.kuis.update', $kuis);
    })->name('guru.kuis.update');

    Route::delete('/guru/kuis/{kuis}', function ($kuis) {
        return redirect()->route('dosen.kuis.destroy', $kuis);
    })->name('guru.kuis.destroy');

    Route::post('/guru/kuis/{kuis}/reset', function ($kuis) {
        return redirect()->route('dosen.kuis.reset', $kuis);
    })->name('guru.kuis.reset');

    Route::post('/guru/kuis/{kuis}/questions', function ($kuis) {
        return redirect()->route('dosen.kuis.questions.store', ['kuis' => $kuis]);
    })->name('guru.kuis.questions.store');

    Route::get('/guru/kuis/{kuis}/questions/import', function ($kuis) {
        return redirect()->route('dosen.kuis.show', $kuis);
    })->name('guru.kuis.questions.import');

    Route::post('/guru/kuis/{kuis}/questions/import', [QuizController::class, 'importQuestions']);

    Route::get('/guru/kuis/{kuis}/questions/export', function ($kuis) {
        return redirect()->route('dosen.kuis.questions.export', ['kuis' => $kuis]);
    })->name('guru.kuis.questions.export');

    Route::get('/guru/questions/{question}/edit', function ($question) {
        return redirect()->route('dosen.questions.edit', $question);
    })->name('guru.questions.edit');

    Route::put('/guru/questions/{question}', function ($question) {
        return redirect()->route('dosen.questions.update', $question);
    })->name('guru.questions.update');

    Route::delete('/guru/questions/{question}', function ($question) {
        return redirect()->route('dosen.questions.destroy', $question);
    })->name('guru.questions.destroy');

    Route::get('/guru/kuis/{kuis}/rekap', function ($kuis) {
        return redirect()->route('dosen.kuis.rekap', $kuis);
    })->name('guru.kuis.rekap');

    Route::get('/guru/kuis/{kuis}/rekap/export', function ($kuis) {
        return redirect()->route('dosen.kuis.rekap.export', $kuis);
    })->name('guru.kuis.rekap.export');

    Route::get('/guru/kuis/attempts/{attempt}/edit', function ($attempt) {
        return redirect()->route('dosen.kuis.attempts.edit', $attempt);
    })->name('guru.kuis.attempts.edit');

    Route::match(['patch', 'post'], '/guru/kuis/attempts/{attempt}', function ($attempt) {
        return redirect()->route('dosen.kuis.attempts.update', $attempt);
    })->name('guru.kuis.attempts.update');

    Route::post('/guru/kuis/attempts/{attempt}/grade-answers', function ($attempt) {
        return redirect()->route('dosen.kuis.attempts.grade_answers', $attempt);
    })->name('guru.kuis.attempts.grade_answers');

    Route::get('/guru/kuis/attempts/{attempt}', function ($attempt) {
        return redirect()->route('dosen.kuis.attempts.show', $attempt);
    })->name('guru.kuis.attempts.show');

    Route::get('/guru/kuis/attempts/{attempt}/answers/{answer}/edit', function ($attempt, $answer) {
        return redirect()->route('dosen.kuis.attempts.answers.edit', ['attempt' => $attempt, 'answer' => $answer]);
    })->name('guru.kuis.attempts.answers.edit');

    Route::patch('/guru/kuis/attempts/{attempt}/answers/{answer}', function ($attempt, $answer) {
        return redirect()->route('dosen.kuis.attempts.answers.update', ['attempt' => $attempt, 'answer' => $answer]);
    })->name('guru.kuis.attempts.answers.update');

    Route::get('/guru/problem-based-learning', function () {
        return redirect()->route('dosen.problems.index');
    })->name('guru.problems.index');

    Route::get('/guru/materis', function () {
        return redirect()->route('dosen.materis.index');
    })->name('guru.materis.index');

    Route::get('/guru/discussions', function () {
        return redirect()->route('dosen.discussions.index');
    })->name('guru.discussions.index');

    Route::get('/guru/penilaian', function () {
        return redirect()->route('dosen.penilaian.index');
    })->name('guru.penilaian.index');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'redirect'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Publicly accessible class detail page
        Route::get('/dosen/kelas/{kelas}', [KelasController::class, 'show'])->name('dosen.kelas.show');

        // Publicly accessible materi show page
        Route::get('kelas/{kelas}/materis/{materi}', [MateriController::class, 'show'])->name('kelas.materis.show');

        // Allow all authenticated users to view materi via the dosen URL as well
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('kelas/{kelas}/materis/{materi}', [MateriController::class, 'show'])->name('kelas.materis.show');
        });

        // Live Code Demo Route
        Route::get('/live-code-demo', function () {
            return view('live-code-demo');
        })->name('live-code-demo');

        // Route to view submission files, accessible by authenticated users (student/teacher/admin)
        Route::get('submissions/{submission}/file', [SiswaProblemController::class, 'showSubmissionFile'])->name('submissions.file');
    });

    // Rute untuk Guru
    Route::middleware(['auth', \App\Http\Middleware\GuruMiddleware::class])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('kelas', KelasController::class)->except(['show']);
        Route::get('kelas/{kelas}/mata-kuliah', [KelasController::class, 'mataPelajaran'])->name('kelas.mata-kuliah');
        Route::get('kelas/{kelas}/anggota', [KelasController::class, 'anggota'])->name('kelas.anggota');
        Route::resource('groups', GroupController::class);
        Route::get('kelas/{kelas}/add-student', [KelasController::class, 'addStudent'])->name('kelas.add_student');
        Route::post('kelas/{kelas}/store-student', [KelasController::class, 'storeStudent'])->name('kelas.store_student');
        Route::delete('kelas/{kelas}/remove-student/{student}', [KelasController::class, 'removeStudent'])->name('kelas.remove_student');

        // Routes for subject-specific content within a class
        Route::prefix('kelas/{kelas}/mata-kuliah/{mataPelajaran}')->name('kelas.mata-kuliah.')->group(function () {
            // Materi
            Route::get('materis', [MateriController::class, 'indexByKelasAndMataPelajaran'])->name('materis.index');
            Route::get('materis/create', [MateriController::class, 'createByKelasAndMataPelajaran'])->name('materis.create');
            Route::post('materis', [MateriController::class, 'storeByKelasAndMataPelajaran'])->name('materis.store');

            // Problems (Tugas PBL)
            Route::get('problem-based-learning', [ProblemController::class, 'indexByKelasAndMataPelajaran'])->name('problems.index');
            Route::get('problem-based-learning/create', [ProblemController::class, 'createByKelasAndMataPelajaran'])->name('problems.create');
            Route::post('problem-based-learning', [ProblemController::class, 'storeByKelasAndMataPelajaran'])->name('problems.store');

            // Submissions (Penilaian) - all submissions for problems related to a specific class and subject
            Route::get('submissions', [ProblemController::class, 'indexSubmissionsByKelasAndMataPelajaran'])->name('submissions.index');

            // Discussions
            Route::get('discussions', [DiscussionController::class, 'indexByKelasAndMataPelajaran'])->name('discussions.index');
            Route::get('discussions/create', [DiscussionController::class, 'createByKelasAndMataPelajaran'])->name('discussions.create');
            Route::post('discussions', [DiscussionController::class, 'storeByKelasAndMataPelajaran'])->name('discussions.store');
        });

        // General material creation
        Route::get('materis/create', [MateriController::class, 'createGeneral'])->name('materis.create.general');
        Route::post('materis', [MateriController::class, 'storeGeneral'])->name('materis.store.general');

        // General problem creation
        Route::get('problem-based-learning/create', [ProblemController::class, 'createGeneral'])->name('problems.create.general');
        Route::post('problem-based-learning', [ProblemController::class, 'storeGeneral'])->name('problems.store.general');

        // PBL validation dashboard for guru
        Route::get('pbl-validations', [PblValidationController::class, 'index'])->name('pbl.validations.index');
        Route::post('pbl-validations/{validation}/validate', [PblValidationController::class, 'validateRequest'])->name('pbl.validations.validate');
        Route::post('pbl-validations/validate-all', [PblValidationController::class, 'validateAll'])->name('pbl.validations.validate_all');

        // Explicit routes for materi within kelas
        Route::get('kelas/{kelas}/materis', [MateriController::class, 'index'])->name('kelas.materis.index');
        Route::get('kelas/{kelas}/materis/create', [MateriController::class, 'create'])->name('kelas.materis.create');
        Route::post('kelas/{kelas}/materis', [MateriController::class, 'store'])->name('kelas.materis.store');
        Route::get('kelas/{kelas}/materis/{materi}/files/{materiFile}/view', [MateriController::class, 'viewFile'])->name('kelas.materis.view');
        Route::get('kelas/{kelas}/materis/{materi}/files/{materiFile}/download', [MateriController::class, 'download'])->name('kelas.materis.download');
        Route::get('kelas/{kelas}/materis/{materi}/edit', [MateriController::class, 'edit'])->name('kelas.materis.edit');
        Route::put('kelas/{kelas}/materis/{materi}', [MateriController::class, 'update'])->name('kelas.materis.update');
        Route::delete('kelas/{kelas}/materis/{materi}', [MateriController::class, 'destroy'])->name('kelas.materis.destroy');

        // Route to access materi directly without kelas parameter
        Route::get('materis/{materi}', [MateriController::class, 'showDirect'])->name('materis.show');

        Route::resource('kelas.problems', ProblemController::class)->parameters([
            'problems' => 'problem',
            'kelas' => 'kelas'
        ]);

        // Non-nested convenience routes for problems (allow edit/update/delete without passing kelas id)
        // These delegate to controller methods that resolve the related kelas from the Problem model.
        Route::get('problems/{problem}/edit', [ProblemController::class, 'editSimple'])->name('problems.edit');
        Route::put('problems/{problem}', [ProblemController::class, 'updateGeneral'])->name('problems.update');
        Route::delete('problems/{problem}', [ProblemController::class, 'destroyGeneral'])->name('problems.destroy');

        // Non-nested show route: redirect to the problem-based-learning index
        Route::get('problems/{problem}', function () {
            return redirect()->route('dosen.problems.index');
        })->name('problems.show');

        // Rute untuk melihat submissions dari sebuah problem
        Route::get('kelas/{kelas}/problems/{problem}/submissions', [ProblemController::class, 'indexSubmissions'])->name('kelas.problems.submissions.index');
        // Rute untuk melihat detail dan menilai submission tunggal
        Route::get('kelas/{kelas}/problems/{problem}/submissions/{submission}', [ProblemController::class, 'showSubmission'])->name('kelas.problems.submissions.show');
        Route::patch('kelas/{kelas}/problems/{problem}/submissions/{submission}', [ProblemController::class, 'gradeSubmission'])->name('kelas.problems.submissions.grade');

        // New routes for Guru
        Route::get('materis', [MateriController::class, 'index'])->name('materis.index');
        Route::delete('materis/pertemuan/{number}', [MateriController::class, 'destroyPertemuan'])->name('materis.pertemuan.destroy');
        Route::post('materis/pertemuan', [MateriController::class, 'storePertemuan'])->name('materis.pertemuan.store');
        Route::patch('problem-based-learning/{problem}/stages/{stage}/validate', [ProblemController::class, 'validateStage'])->name('problems.stages.validate');
        Route::get('problem-based-learning', [ProblemController::class, 'index'])->name('problems.index');
        // PBL modal details and validation via AJAX
        Route::get('problems/{problem}/pbl-details', [ProblemController::class, 'pblDetails']);
        // Organisasi page for a problem (guru)
        Route::get('problems/{problem}/organisasi', [ProblemController::class, 'organisasi'])->name('problems.organisasi');
        // Store instruction for a problem (AJAX)
        Route::post('problems/{problem}/instructions', [ProblemController::class, 'addInstruction'])->name('problems.instructions.store');
        // Delete an instruction for a problem (AJAX)
        Route::delete('problems/{problem}/instructions/{stage}/{index}', [ProblemController::class, 'deleteInstruction'])->name('problems.instructions.destroy');
        Route::patch('problems/{problem}/instructions/{stage}/{index}', [ProblemController::class, 'updateInstruction'])->name('problems.instructions.update');
        Route::post('problems/{problem}/pbl-validate', [ProblemController::class, 'pblValidate']);
        // Save teacher feedback for a student on a problem
        Route::post('problems/{problem}/student-feedback', [ProblemController::class, 'storeStudentFeedback']);
        // Save teacher grading (rubric) for a student on a problem (stage 5)
        Route::post('problems/{problem}/student-grade', [ProblemController::class, 'storeStudentGrade']);
        // Save teacher evaluation (4 criteria) for a student on a problem
        Route::post('problems/{problem}/student-evaluation', [ProblemController::class, 'storeStudentEvaluation']);
        Route::get('submissions', [App\Http\Controllers\Guru\SubmissionController::class, 'index'])->name('submissions.index');
        
        // Penilaian
        Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');

        // Discussion Routes
        Route::get('discussions', [DiscussionController::class, 'indexAll'])->name('discussions.index');
        // General discussion creation
        Route::get('discussions/create', [DiscussionController::class, 'createGeneral'])->name('discussions.create.general');
        Route::post('discussions/store', [DiscussionController::class, 'storeGeneral'])->name('discussions.store.general');

        Route::resource('problems.discussions', DiscussionController::class)->except(['index'])->shallow();
        Route::get('problems/{problem}/discussions', [DiscussionController::class, 'index'])->name('problems.discussions.index');
        Route::post('discussions/{discussion}/posts', [DiscussionController::class, 'storePost'])->name('discussions.posts.store');

        // New route for fetching mata kuliah by kelas
        Route::get('get-mata-kuliah-by-kelas/{kelas}', [MateriController::class, 'getMataPelajaranByKelas'])->name('get-mata-kuliah-by-kelas');
    });

    // Temporary debug route to check Zip extension in the web SAPI
    Route::get('/_debug/phpzip', function () {
        return response()->json([
            'zip_loaded' => extension_loaded('zip'),
            'php_ini' => php_ini_loaded_file(),
            'sapi' => php_sapi_name(),
        ]);
    });

    // Debug route to check materi access
    Route::get('/_debug/materi/{materi_id}', function ($materi_id) {
        try {
            $materi = \App\Models\Materi::find($materi_id);
            
            if (!$materi) {
                return response()->json([
                    'error' => 'Materi not found',
                    'materi_id' => $materi_id,
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'materi_id' => $materi->id,
                'judul' => $materi->judul,
                'kelas_id' => $materi->kelas_id,
                'kelas' => $materi->kelas,
                'user_id' => auth()->id(),
                'user_roles' => auth()->user() ? auth()->user()->roles()->pluck('name')->toArray() : [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    })->name('debug.materi');

    // Debug route to check quiz attempt updates
    Route::get('/_debug/quiz-attempt-logs/{attempt_id}', function ($attempt_id) {
        try {
            $logDirectory = storage_path('logs');
            $logFiles = glob($logDirectory . DIRECTORY_SEPARATOR . '*.log');
            
            if (empty($logFiles)) {
                return response()->json([
                    'error' => 'No log files found in storage/logs',
                    'path' => $logDirectory,
                ], 404);
            }
            
            $attempt = \App\Models\QuizAttempt::find($attempt_id);
            if (!$attempt) {
                return response()->json([
                    'error' => 'Attempt not found',
                    'attempt_id' => $attempt_id,
                ], 404);
            }
            
            $attemptIdPattern = preg_quote((string) $attempt_id, '/');
            $relevantLogs = [];
            $scannedFiles = [];
            $totalSize = 0;

            foreach ($logFiles as $logFile) {
                if (!is_file($logFile) || !is_readable($logFile)) {
                    continue;
                }

                $scannedFiles[] = $logFile;
                $totalSize += filesize($logFile);
                $lines = explode("\n", file_get_contents($logFile));

                foreach ($lines as $line) {
                    if (strpos($line, 'updateAttempt') !== false
                        || preg_match('/"attempt_id"\s*:\s*' . $attemptIdPattern . '\b/', $line)
                        || preg_match('/\battempt_id\b.*\b' . $attemptIdPattern . '\b/', $line)
                    ) {
                        $relevantLogs[] = trim($line);
                    }
                }
            }
            
            // Get last 20 entries
            $relevantLogs = array_slice($relevantLogs, -20);
            
            return response()->json([
                'attempt_id' => $attempt_id,
                'scanned_log_files' => $scannedFiles,
                'attempt_current_state' => [
                    'score' => $attempt->score,
                    'status' => $attempt->status,
                    'start_time' => $attempt->start_time ? $attempt->start_time->toIso8601String() : null,
                    'end_time' => $attempt->end_time ? $attempt->end_time->toIso8601String() : null,
                    'updated_at' => $attempt->updated_at ? $attempt->updated_at->toIso8601String() : null,
                ],
                'recent_logs' => $relevantLogs,
                'total_log_size_bytes' => $totalSize,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    })->name('debug.quiz.attempt.logs');

    // New group for Kuis routes without GuruMiddleware
    Route::middleware(['auth'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('kuis', [QuizController::class, 'index'])->name('kuis.index');
        Route::get('kuis/create', [QuizController::class, 'create'])->name('kuis.create');
        Route::post('kuis', [QuizController::class, 'store'])->name('kuis.store');
        Route::get('kuis/{kuis}', [QuizController::class, 'show'])->name('kuis.show');
        Route::get('kuis/{kuis}/edit', [QuizController::class, 'edit'])->name('kuis.edit');
        Route::put('kuis/{kuis}', [QuizController::class, 'update'])->name('kuis.update');
        Route::delete('kuis/{kuis}', [QuizController::class, 'destroy'])->name('kuis.destroy');
        Route::post('kuis/{kuis}/reset', [QuizController::class, 'resetAttempts'])->name('kuis.reset');

        

        // Routes for Quiz Questions

        Route::get('kuis/{kuis}/questions/create', [QuizController::class, 'createQuestion'])->name('kuis.questions.create');

        // Redirect direct GET access to the import URL back to the quiz show page
        Route::get('kuis/{kuis}/questions/import', function ($kuis) {
            return redirect()->route('dosen.kuis.show', ['kuis' => $kuis]);
        });

        Route::post('kuis/{kuis}/questions/import', [QuizController::class, 'importQuestions'])->name('kuis.questions.import');

        // Export quiz questions to Excel/CSV
        Route::get('kuis/{kuis}/questions/export', [QuizController::class, 'exportQuestions'])->name('kuis.questions.export');

        Route::post('kuis/{kuis}/questions', [QuizController::class, 'storeQuestion'])->name('kuis.questions.store');

        Route::get('questions/{question}/edit', [QuizController::class, 'editQuestion'])->name('questions.edit');

        Route::put('questions/{question}', [QuizController::class, 'updateQuestion'])->name('questions.update');

        Route::delete('questions/{question}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');

        // Rekap hasil kuis (Guru)
        Route::get('kuis/{kuis}/rekap', [QuizController::class, 'rekap'])->name('kuis.rekap');
        Route::get('kuis/{kuis}/rekap/export', [QuizController::class, 'exportRekap'])->name('kuis.rekap.export');
        Route::get('kuis/attempts/{attempt}/edit', [QuizController::class, 'editAttempt'])->name('kuis.attempts.edit');
        Route::match(['patch', 'post'], 'kuis/attempts/{attempt}', [QuizController::class, 'updateAttempt'])->name('kuis.attempts.update');
        Route::post('kuis/attempts/{attempt}/grade-answers', [QuizController::class, 'gradeAnswers'])->name('kuis.attempts.grade_answers');
        Route::get('kuis/attempts/{attempt}', [QuizController::class, 'showAttempt'])->name('kuis.attempts.show');
        Route::delete('kuis/attempts/{attempt}', [QuizController::class, 'destroyAttempt'])->name('kuis.attempts.destroy');
        // Routes for editing a single answer belonging to an attempt
        Route::get('kuis/attempts/{attempt}/answers/{answer}/edit', [QuizController::class, 'editAnswer'])->name('kuis.attempts.answers.edit');
        Route::patch('kuis/attempts/{attempt}/answers/{answer}', [QuizController::class, 'updateAnswer'])->name('kuis.attempts.answers.update');
    });

    // Rute untuk Siswa
    Route::middleware(['auth', \App\Http\Middleware\SiswaMiddleware::class])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('kelas', [SiswaKelasController::class, 'index'])->name('kelas.index');
        Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('pengumumans', [App\Http\Controllers\Siswa\PengumumanController::class, 'index'])->name('pengumumans.index');
        Route::get('pengumumans/{pengumuman}', [App\Http\Controllers\Siswa\PengumumanController::class, 'show'])->name('pengumumans.show');
        Route::get('problem-based-learning', [SiswaProblemController::class, 'index'])->name('problems.index');
        Route::get('problem-based-learning/{problem}', [SiswaProblemController::class, 'show'])->name('problems.show');
        Route::post('problems/{problem}/pbl/request-validation', [PblController::class, 'requestValidation'])->name('problems.pbl.request')->middleware('auth');
        Route::get('problems/{problem}/pbl/status', [PblController::class, 'status'])->name('problems.pbl.status')->middleware('auth');
        Route::post('problem-based-learning/{problem}/stages/{stage}/complete', [SiswaProblemController::class, 'completeStage'])->name('problems.stages.complete');
        Route::get('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'createSubmission'])->name('problems.submit.create');
        Route::post('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'storeSubmission'])->name('problems.submit.store');
        // Quick feedback (content-only) submission from problem show page
        Route::post('problem-based-learning/{problem}/feedback', [SiswaProblemController::class, 'storeFeedback'])->name('problems.feedback.store');
        // Student self-evaluation rubric for stage 5
        Route::post('problem-based-learning/{problem}/rubric', [SiswaProblemController::class, 'storeRubric'])->name('problems.rubric.store');
        Route::get('problem-based-learning/{problem}/submit/edit', [SiswaProblemController::class, 'editSubmission'])->name('problems.submit.edit');
        Route::patch('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'updateSubmission'])->name('problems.submit.update');
        Route::get('submissions', [SiswaProblemController::class, 'indexSubmissions'])->name('submissions.index');

        // Rute untuk Mata Kuliah Siswa
        Route::get('mata-kuliah/{mataPelajaran}', [SiswaMataPelajaranController::class, 'show'])->name('mata-kuliah.show');
        Route::get('mata-kuliah', [SiswaMataPelajaranController::class, 'index'])->name('mata-kuliah.index');

        // Rute untuk Materi Siswa
        Route::get('materis', [App\Http\Controllers\Siswa\MateriController::class, 'index'])->name('materis.index');

        // Rute untuk Enrollment Siswa
        Route::get('enroll', [SiswaProblemController::class, 'createEnrollment'])->name('enroll.create');
        Route::post('enroll', [SiswaProblemController::class, 'storeEnrollment'])->name('enroll.store');

        // Rute untuk Refleksi Diri Siswa
        Route::resource('refleksi', App\Http\Controllers\Siswa\RefleksiController::class);

        // Rute untuk Materi Siswa
        Route::get('kelas/{kelas}/materi/{materi}', [App\Http\Controllers\Siswa\MateriController::class, 'show'])->name('materi.show');
        Route::get('kelas/{kelas}/materi/{materi}/files/{materiFile}/view', [App\Http\Controllers\Siswa\MateriController::class, 'viewFile'])->name('materi.viewFile');
        Route::get('kelas/{kelas}/materi/{materi}/files/{materiFile}/download', [App\Http\Controllers\Siswa\MateriController::class, 'downloadFile'])->name('materi.downloadFile');

        // Rute untuk Problem Siswa
        Route::get('kelas/{kelas}/problem/{problem}', [App\Http\Controllers\Siswa\ProblemController::class, 'show'])->name('problem.show');

        // Discussion Routes for Siswa
        Route::get('discussions', [App\Http\Controllers\Siswa\DiscussionController::class, 'index'])->name('discussions.index');
        Route::post('discussions', [App\Http\Controllers\Siswa\DiscussionController::class, 'storeGeneral'])->name('discussions.store');
        Route::get('problem-based-learning/{problem}/discussions', [\App\Http\Controllers\Siswa\DiscussionController::class, 'index'])->name('problems.discussions.index');
        Route::get('problem-based-learning/{problem}/discussions/create', [\App\Http\Controllers\Siswa\DiscussionController::class, 'create'])->name('problems.discussions.create');
        Route::post('problem-based-learning/{problem}/discussions', [\App\Http\Controllers\Siswa\DiscussionController::class, 'store'])->name('problems.discussions.store');
        Route::get('discussions/{discussion}', [\App\Http\Controllers\Siswa\DiscussionController::class, 'show'])->name('discussions.show');
        Route::post('discussions/{discussion}/posts', [\App\Http\Controllers\Siswa\DiscussionController::class, 'storePost'])->name('discussions.posts.store');

        Route::get('kelompok', [SiswaKelompokController::class, 'index'])->name('kelompok.index');
        Route::get('kelompok/{kelompok}', [SiswaKelompokController::class, 'show'])->name('kelompok.show');
        Route::post('kelompok/{kelompok}/join', [SiswaKelompokController::class, 'join'])->name('kelompok.join');
        Route::get('penilaian', [SiswaPenilaianController::class, 'index'])->name('penilaian.index');

        // Rute untuk Kuis Siswa
        Route::resource('quizzes', SiswaQuizController::class)->only(['index', 'show']);
        Route::post('quizzes/{quiz}/submit', [SiswaQuizController::class, 'submit'])->name('quizzes.submit');

        // Rute untuk Java Compiler
        Route::prefix('compiler')->name('compiler.')->group(function () {
            Route::get('/', [SiswaProblemController::class, 'compiler'])->name('index');
            Route::post('run', [SiswaProblemController::class, 'runCode'])->name('run');
        });
    });

    // Backward compatibility: old route names for legacy views/controllers
    Route::middleware(['auth', \App\Http\Middleware\GuruMiddleware::class])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Guru\DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('kelas', KelasController::class)->except(['show']);
        Route::get('kelas/{kelas}/mata-kuliah', [KelasController::class, 'mataPelajaran'])->name('kelas.mata-kuliah');
        Route::get('kelas/{kelas}/anggota', [KelasController::class, 'anggota'])->name('kelas.anggota');
        Route::resource('groups', GroupController::class);
        Route::get('kelas/{kelas}/add-student', [KelasController::class, 'addStudent'])->name('kelas.add_student');
        Route::post('kelas/{kelas}/store-student', [KelasController::class, 'storeStudent'])->name('kelas.store_student');
        Route::delete('kelas/{kelas}/remove-student/{student}', [KelasController::class, 'removeStudent'])->name('kelas.remove_student');

        Route::prefix('kelas/{kelas}/mata-kuliah/{mataPelajaran}')->name('kelas.mata-kuliah.')->group(function () {
            Route::get('materis', [MateriController::class, 'indexByKelasAndMataPelajaran'])->name('materis.index');
            Route::get('materis/create', [MateriController::class, 'createByKelasAndMataPelajaran'])->name('materis.create');
            Route::post('materis', [MateriController::class, 'storeByKelasAndMataPelajaran'])->name('materis.store');
            Route::get('problem-based-learning', [ProblemController::class, 'indexByKelasAndMataPelajaran'])->name('problems.index');
            Route::get('problem-based-learning/create', [ProblemController::class, 'createByKelasAndMataPelajaran'])->name('problems.create');
            Route::post('problem-based-learning', [ProblemController::class, 'storeByKelasAndMataPelajaran'])->name('problems.store');
            Route::get('submissions', [ProblemController::class, 'indexSubmissionsByKelasAndMataPelajaran'])->name('submissions.index');
            Route::get('discussions', [DiscussionController::class, 'indexByKelasAndMataPelajaran'])->name('discussions.index');
            Route::get('discussions/create', [DiscussionController::class, 'createByKelasAndMataPelajaran'])->name('discussions.create');
            Route::post('discussions', [DiscussionController::class, 'storeByKelasAndMataPelajaran'])->name('discussions.store');
        });

        Route::get('materis/create', [MateriController::class, 'createGeneral'])->name('materis.create.general');
        Route::post('materis', [MateriController::class, 'storeGeneral'])->name('materis.store.general');
        Route::get('problem-based-learning/create', [ProblemController::class, 'createGeneral'])->name('problems.create.general');
        Route::post('problem-based-learning', [ProblemController::class, 'storeGeneral'])->name('problems.store.general');
        Route::get('pbl-validations', [PblValidationController::class, 'index'])->name('pbl.validations.index');
        Route::post('pbl-validations/{validation}/validate', [PblValidationController::class, 'validateRequest'])->name('pbl.validations.validate');
        Route::post('pbl-validations/validate-all', [PblValidationController::class, 'validateAll'])->name('pbl.validations.validate_all');
        Route::get('kelas/{kelas}/materis', [MateriController::class, 'index'])->name('kelas.materis.index');
        Route::get('kelas/{kelas}/materis/create', [MateriController::class, 'create'])->name('kelas.materis.create');
        Route::post('kelas/{kelas}/materis', [MateriController::class, 'store'])->name('kelas.materis.store');
        Route::get('kelas/{kelas}/materis/{materi}/files/{materiFile}/view', [MateriController::class, 'viewFile'])->name('kelas.materis.view');
        Route::get('kelas/{kelas}/materis/{materi}/files/{materiFile}/download', [MateriController::class, 'download'])->name('kelas.materis.download');
        Route::get('kelas/{kelas}/materis/{materi}/edit', [MateriController::class, 'edit'])->name('kelas.materis.edit');
        Route::put('kelas/{kelas}/materis/{materi}', [MateriController::class, 'update'])->name('kelas.materis.update');
        Route::delete('kelas/{kelas}/materis/{materi}', [MateriController::class, 'destroy'])->name('kelas.materis.destroy');
        Route::resource('kelas.problems', ProblemController::class)->parameters(['problems' => 'problem', 'kelas' => 'kelas']);
        Route::get('problems/{problem}/edit', [ProblemController::class, 'editSimple'])->name('problems.edit');
        Route::put('problems/{problem}', [ProblemController::class, 'updateGeneral'])->name('problems.update');
        Route::delete('problems/{problem}', [ProblemController::class, 'destroyGeneral'])->name('problems.destroy');
        Route::get('problems/{problem}', function () { return redirect()->route('guru.problems.index'); })->name('problems.show');
        Route::get('kelas/{kelas}/problems/{problem}/submissions', [ProblemController::class, 'indexSubmissions'])->name('kelas.problems.submissions.index');
        Route::get('kelas/{kelas}/problems/{problem}/submissions/{submission}', [ProblemController::class, 'showSubmission'])->name('kelas.problems.submissions.show');
        Route::patch('kelas/{kelas}/problems/{problem}/submissions/{submission}', [ProblemController::class, 'gradeSubmission'])->name('kelas.problems.submissions.grade');
        Route::get('materis', [MateriController::class, 'index'])->name('materis.index');
        Route::delete('materis/pertemuan/{number}', [MateriController::class, 'destroyPertemuan'])->name('materis.pertemuan.destroy');
        Route::post('materis/pertemuan', [MateriController::class, 'storePertemuan'])->name('materis.pertemuan.store');
        Route::patch('problem-based-learning/{problem}/stages/{stage}/validate', [ProblemController::class, 'validateStage'])->name('problems.stages.validate');
        Route::get('problem-based-learning', [ProblemController::class, 'index'])->name('problems.index');
        Route::get('problems/{problem}/pbl-details', [ProblemController::class, 'pblDetails']);
        Route::get('problems/{problem}/organisasi', [ProblemController::class, 'organisasi'])->name('problems.organisasi');
        Route::post('problems/{problem}/instructions', [ProblemController::class, 'addInstruction'])->name('problems.instructions.store');
        Route::delete('problems/{problem}/instructions/{stage}/{index}', [ProblemController::class, 'deleteInstruction'])->name('problems.instructions.destroy');
        Route::patch('problems/{problem}/instructions/{stage}/{index}', [ProblemController::class, 'updateInstruction'])->name('problems.instructions.update');
        Route::post('problems/{problem}/pbl-validate', [ProblemController::class, 'pblValidate']);
        Route::post('problems/{problem}/student-feedback', [ProblemController::class, 'storeStudentFeedback']);
        Route::post('problems/{problem}/student-grade', [ProblemController::class, 'storeStudentGrade']);
        Route::post('problems/{problem}/student-evaluation', [ProblemController::class, 'storeStudentEvaluation']);
        Route::get('submissions', [App\Http\Controllers\Guru\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('discussions', [DiscussionController::class, 'indexAll'])->name('discussions.index');
        Route::get('discussions/create', [DiscussionController::class, 'createGeneral'])->name('discussions.create.general');
        Route::post('discussions/store', [DiscussionController::class, 'storeGeneral'])->name('discussions.store.general');
        Route::resource('problems.discussions', DiscussionController::class)->except(['index'])->shallow();
        Route::get('problems/{problem}/discussions', [DiscussionController::class, 'index'])->name('problems.discussions.index');
        Route::post('discussions/{discussion}/posts', [DiscussionController::class, 'storePost'])->name('discussions.posts.store');
        Route::get('get-mata-kuliah-by-kelas/{kelas}', [MateriController::class, 'getMataPelajaranByKelas'])->name('get-mata-kuliah-by-kelas');
    });

    Route::middleware(['auth', \App\Http\Middleware\SiswaMiddleware::class])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('kelas', [SiswaKelasController::class, 'index'])->name('kelas.index');
        Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('pengumumans', [App\Http\Controllers\Siswa\PengumumanController::class, 'index'])->name('pengumumans.index');
        Route::get('pengumumans/{pengumuman}', [App\Http\Controllers\Siswa\PengumumanController::class, 'show'])->name('pengumumans.show');
        Route::get('problem-based-learning', [SiswaProblemController::class, 'index'])->name('problems.index');
        Route::get('problem-based-learning/{problem}', [SiswaProblemController::class, 'show'])->name('problems.show');
        Route::post('problems/{problem}/pbl/request-validation', [PblController::class, 'requestValidation'])->name('problems.pbl.request')->middleware('auth');
        Route::get('problems/{problem}/pbl/status', [PblController::class, 'status'])->name('problems.pbl.status')->middleware('auth');
        Route::post('problem-based-learning/{problem}/stages/{stage}/complete', [SiswaProblemController::class, 'completeStage'])->name('problems.stages.complete');
        Route::get('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'createSubmission'])->name('problems.submit.create');
        Route::post('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'storeSubmission'])->name('problems.submit.store');
        Route::post('problem-based-learning/{problem}/feedback', [SiswaProblemController::class, 'storeFeedback'])->name('problems.feedback.store');
        Route::post('problem-based-learning/{problem}/rubric', [SiswaProblemController::class, 'storeRubric'])->name('problems.rubric.store');
        Route::get('problem-based-learning/{problem}/submit/edit', [SiswaProblemController::class, 'editSubmission'])->name('problems.submit.edit');
        Route::patch('problem-based-learning/{problem}/submit', [SiswaProblemController::class, 'updateSubmission'])->name('problems.submit.update');
        Route::get('submissions', [SiswaProblemController::class, 'indexSubmissions'])->name('submissions.index');
        Route::get('mata-kuliah/{mataPelajaran}', [SiswaMataPelajaranController::class, 'show'])->name('mata-kuliah.show');
        Route::get('mata-kuliah', [SiswaMataPelajaranController::class, 'index'])->name('mata-kuliah.index');
        Route::get('materis', [App\Http\Controllers\Siswa\MateriController::class, 'index'])->name('materis.index');
        Route::get('enroll', [SiswaProblemController::class, 'createEnrollment'])->name('enroll.create');
        Route::post('enroll', [SiswaProblemController::class, 'storeEnrollment'])->name('enroll.store');
        Route::resource('refleksi', App\Http\Controllers\Siswa\RefleksiController::class);
        Route::get('kelas/{kelas}/materi/{materi}', [App\Http\Controllers\Siswa\MateriController::class, 'show'])->name('materi.show');
        Route::get('kelas/{kelas}/materi/{materi}/files/{materiFile}/view', [App\Http\Controllers\Siswa\MateriController::class, 'viewFile'])->name('materi.viewFile');
        Route::get('kelas/{kelas}/materi/{materi}/files/{materiFile}/download', [App\Http\Controllers\Siswa\MateriController::class, 'downloadFile'])->name('materi.downloadFile');
        Route::get('kelas/{kelas}/problem/{problem}', [App\Http\Controllers\Siswa\ProblemController::class, 'show'])->name('problem.show');
        Route::get('discussions', [App\Http\Controllers\Siswa\DiscussionController::class, 'index'])->name('discussions.index');
        Route::post('discussions', [App\Http\Controllers\Siswa\DiscussionController::class, 'storeGeneral'])->name('discussions.store');
        Route::get('problem-based-learning/{problem}/discussions', [\App\Http\Controllers\Siswa\DiscussionController::class, 'index'])->name('problems.discussions.index');
        Route::get('problem-based-learning/{problem}/discussions/create', [\App\Http\Controllers\Siswa\DiscussionController::class, 'create'])->name('problems.discussions.create');
        Route::post('problem-based-learning/{problem}/discussions', [\App\Http\Controllers\Siswa\DiscussionController::class, 'store'])->name('problems.discussions.store');
        Route::get('discussions/{discussion}', [\App\Http\Controllers\Siswa\DiscussionController::class, 'show'])->name('discussions.show');
        Route::post('discussions/{discussion}/posts', [\App\Http\Controllers\Siswa\DiscussionController::class, 'storePost'])->name('discussions.posts.store');
        Route::get('kelompok', [SiswaKelompokController::class, 'index'])->name('kelompok.index');
        Route::get('kelompok/{kelompok}', [SiswaKelompokController::class, 'show'])->name('kelompok.show');
        Route::post('kelompok/{kelompok}/join', [SiswaKelompokController::class, 'join'])->name('kelompok.join');
        Route::get('penilaian', [SiswaPenilaianController::class, 'index'])->name('penilaian.index');
        Route::resource('quizzes', SiswaQuizController::class)->only(['index', 'show']);
        Route::post('quizzes/{quiz}/submit', [SiswaQuizController::class, 'submit'])->name('quizzes.submit');
        Route::prefix('compiler')->name('compiler.')->group(function () {
            Route::get('/', [SiswaProblemController::class, 'compiler'])->name('index');
            Route::post('run', [SiswaProblemController::class, 'runCode'])->name('run');
        });
    });

    // Rute untuk Admin
    Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');

        Route::resource('users', App\Http\Controllers\Admin\AdminUserController::class);
        // Bulk import users from Excel
        Route::post('users/import', [App\Http\Controllers\Admin\AdminUserController::class, 'import'])->name('users.import');
        Route::resource('mata-kuliah', App\Http\Controllers\Admin\MataPelajaranController::class);
        Route::resource('kelas', App\Http\Controllers\Admin\AdminKelasController::class)->parameters([
            'kelas' => 'kelas'
        ]);
        Route::post('kelas/{kelas}/mata-kuliah', [App\Http\Controllers\Admin\AdminKelasController::class, 'addMataPelajaran'])->name('kelas.mata-kuliah.add');
        Route::delete('kelas/{kelas}/mata-kuliah/{mataPelajaran}', [App\Http\Controllers\Admin\AdminKelasController::class, 'removeMataPelajaran'])->name('kelas.mata-kuliah.remove');
        Route::get('kelas/{kelas}/students', [App\Http\Controllers\Admin\AdminKelasController::class, 'showStudents'])->name('kelas.students');
        Route::post('kelas/{kelas}/mata-kuliah/{mataPelajaran}/assign-guru', [App\Http\Controllers\Admin\AdminKelasController::class, 'assignGuru'])->name('kelas.assignGuru');
        Route::resource('tahun-ajaran', App\Http\Controllers\Admin\TahunAjaranController::class);
        Route::resource('tahun-ajaran.semester', App\Http\Controllers\Admin\SemesterController::class)->shallow();
        // Pengumuman admin removed
        Route::get('submissions', [App\Http\Controllers\Admin\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('problems', [App\Http\Controllers\Admin\ProblemController::class, 'index'])->name('problems.index');
        Route::get('materis', [App\Http\Controllers\Admin\MateriController::class, 'index'])->name('materis.index');
        Route::get('discussions', [App\Http\Controllers\Admin\DiscussionController::class, 'index'])->name('discussions.index');
        Route::get('discussions/{discussion}', [App\Http\Controllers\Admin\DiscussionController::class, 'show'])->name('discussions.show');
        Route::resource('quizzes', App\Http\Controllers\Admin\QuizController::class);
        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings');
        Route::resource('courses', App\Http\Controllers\Admin\CourseController::class);

        // Student Management Routes for Admin
        Route::post('kelas/{kelas}/students', [App\Http\Controllers\Admin\AdminKelasController::class, 'storeStudent'])->name('kelas.storeStudent');
        Route::delete('kelas/{kelas}/students/{student}', [App\Http\Controllers\Admin\AdminKelasController::class, 'removeStudent'])->name('kelas.removeStudent');
    });


    require __DIR__.'/auth.php';