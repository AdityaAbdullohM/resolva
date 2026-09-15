<x-app-layout>

    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <a href="{{ (request()->has('stage') && intval(request('stage')) >= 1 && intval(request('stage')) <= 5) ? url('/dosen/problem-based-learning') : url()->previous() }}" class="inline-flex items-center text-indigo-200 hover:text-white mb-2">
                            &larr; Kembali
                        </a>
                        <h1 class="text-3xl font-extrabold tracking-tight">{{ $problem->judul }}</h1>
                        @php
                            $pblNames = [
                                '1. Orientasi pada masalah',
                                '2. Mengorganisasikan belajar',
                                '3. Penyelidikan individual maupun kelompok',
                                '4. Mengembangkan dan menyajikan hasil karya',
                                '5. Menganalisis dan mengevaluasi proses pemecahan masalah'
                            ];
                            $currentStage = intval(request('stage', 1));
                            $currentStageName = $pblNames[$currentStage - 1] ?? null;
                        @endphp
                        @if(request()->has('stage'))
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-sm font-semibold">Tahap: {{ $currentStageName }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-sm font-semibold">Kelas: {{ $problem->kelas->nama }}</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-sm font-semibold">Mapel: {{ $problem->mataPelajaran->nama }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="-mt-10 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main column -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Deskripsi Tugas</h2>
                        <div class="prose dark:prose-invert text-gray-700 dark:text-gray-300 max-w-none">
                            {!! nl2br(e($problem->deskripsi)) !!}
                        </div>

                        

                
                          

                        

                        

                        

                        @if(request('stage') == 2)
                            <div class="mt-6 border-t pt-5">
                                <h3 class="text-lg font-semibold mb-3">Instruksi Belajar</h3>
                                @php
                                    $stage = 2;
                                    $stageInstructions = data_get($problem->instructions, $stage, []);
                                @endphp
                                @if(!empty($stageInstructions) && is_array($stageInstructions))
                                    <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                        <ul class="space-y-3">
                                            @foreach($stageInstructions as $idx => $inst)
                                                <li class="flex items-start gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-semibold flex items-center justify-center">{{ $idx + 1 }}</div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!}</div>
                                                        @if(!empty(data_get($inst,'created_at')))
                                                            <div class="text-xs text-gray-400 mt-2">— {{ data_get($inst,'created_at') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col items-end gap-2">
                                                        <div class="flex items-center gap-2">
                                                            <button data-stage="{{ $stage }}" data-index="{{ $idx }}" class="edit-instruction text-xs text-indigo-600 hover:underline">Edit</button>
                                                            <button data-stage="{{ $stage }}" data-index="{{ $idx }}" class="delete-instruction text-xs text-red-600 hover:underline">Hapus</button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">Belum ada instruksi belajar untuk tahap ini.</div>
                                @endif

                                <div class="mt-4">
                                    <button id="add-instruction" data-problem-id="{{ $problem->id }}" data-stage="2" class="inline-flex items-center px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Tambah Instruksi</button>
                                </div>
                            </div>
                            @endif

                            @if(request('stage') == 3)
                                <div class="mt-6 border-t pt-5">
                                    <h3 class="text-lg font-semibold mb-3">Instruksi Penyelidikan</h3>
                                    @php
                                        $stage = 3;
                                        $stageInstructions = data_get($problem->instructions, $stage, []);
                                    @endphp
                                    @if(!empty($stageInstructions) && is_array($stageInstructions))
                                        <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                            <ul class="space-y-3">
                                                @foreach($stageInstructions as $idx => $inst)
                                                    <li class="flex items-start gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-semibold flex items-center justify-center">{{ $idx + 1 }}</div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!}</div>
                                                            @if(!empty(data_get($inst,'created_at')))
                                                                <div class="text-xs text-gray-400 mt-2">— {{ data_get($inst,'created_at') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-start">
                                                            <button data-stage="{{ $stage }}" data-index="{{ $idx }}" class="delete-instruction text-xs text-red-600 hover:underline">Hapus</button>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Belum ada instruksi penyelidikan untuk tahap ini.</div>
                                    @endif

                                    <div class="mt-4">
                                        <button id="add-instruction" data-problem-id="{{ $problem->id }}" data-stage="3" class="inline-flex items-center px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Tambah Instruksi</button>
                                    </div>
                                </div>
                            @endif

                            @if(request('stage') == 4)
                                <div class="mt-6 border-t pt-5">
                                    <h3 class="text-lg font-semibold mb-3">Instruksi Mengembangkan dan Menyajikan</h3>
                                    @php
                                        $stage = 4;
                                        $stageInstructions = data_get($problem->instructions, $stage, []);
                                    @endphp
                                    @if(!empty($stageInstructions) && is_array($stageInstructions))
                                        <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                            <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                                    @foreach($stageInstructions as $idx => $inst)
                                                        <li class="flex justify-between items-start">
                                                            <div class="mr-3">{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!} @if(!empty(data_get($inst,'created_at'))) <span class="text-xs text-gray-400">— {{ data_get($inst,'created_at') }}</span>@endif</div>
                                                            <div>
                                                                <button data-stage="{{ $stage }}" data-index="{{ $idx }}" class="delete-instruction text-xs text-red-600 hover:underline">Hapus</button>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">Belum ada instruksi untuk tahap ini.</div>
                                    @endif

                                    <div class="mt-4">
                                        <button id="add-instruction" data-problem-id="{{ $problem->id }}" data-stage="4" class="inline-flex items-center px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Tambah Instruksi</button>
                                    </div>
                                </div>
                            @endif

                        @php
                            $hasFiles = (isset($problem->files) && $problem->files->isNotEmpty());
                            $hasLinks = !empty($problem->links);
                        @endphp

                        @if($hasFiles || $hasLinks)
                            <div class="mt-6 border-t pt-5">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Lampiran & Link</h3>
                                <div class="space-y-3">
                                    @if($hasFiles)
                                        <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                            <ul class="space-y-2">
                                                @foreach($problem->files as $f)
                                                    @php
                                                        $filePath = data_get($f, 'file_path') ?? data_get($f, 'path') ?? null;
                                                        $originalName = data_get($f, 'original_name') ?? data_get($f, 'originalName') ?? null;
                                                    @endphp
                                                    <li>
                                                        @if($filePath)
                                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline font-medium">{{ $originalName ?? basename($filePath) }}</a>
                                                        @else
                                                            <span class="text-gray-600 dark:text-gray-300">{{ $originalName ?? (is_string($f) ? $f : 'File') }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($hasLinks)
                                        <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                            <ul class="space-y-2">
                                                @foreach((array) $problem->links as $ln)
                                                    <li><a href="{{ is_string($ln) ? $ln : (data_get($ln, 'url') ?? '#') }}" target="_blank" class="text-indigo-600 hover:underline font-medium">{{ is_string($ln) ? $ln : (data_get($ln, 'title') ?? data_get($ln, 'url')) }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Students status for teacher -->
                        @php
                            $hasParticipants = isset($students) && $students->isNotEmpty();
                            // prefer participant ids prepared by controller for consistency
                            $participantIds = isset($students) && $students->isNotEmpty() ? $students->pluck('id')->toArray() : \App\Models\Submission::where('problem_id', $problem->id)->pluck('user_id')->unique()->toArray();
                            $groups = \App\Models\Group::where(function($q) use ($problem){
                                $q->whereNull('problem_id')->orWhere('problem_id', $problem->id);
                            })->whereHas('members', function($q) use ($participantIds){ $q->whereIn('users.id', $participantIds); })->get();
                        @endphp
                        @if($hasParticipants)
                        <div class="mt-6 border-t pt-5">
                            <h3 class="text-lg font-semibold mb-3">Status Siswa</h3>

                            @if(request()->has('stage'))
                                @php $stage = intval(request('stage', 1)); @endphp

                                @if($groups->isEmpty())
                                    <div class="text-sm text-gray-500">Tidak ada kelompok yang mengambil problem ini.</div>
                                @else
                                    <div class="space-y-4">
                                        @foreach($groups as $group)
                                            <div class="border rounded p-4 bg-white dark:bg-gray-800">
                                                <div class="mb-2 flex items-center justify-between">
                                                    <div class="font-semibold">Kelompok: {{ $group->name }}</div>
                                                    @php
                                                        $memberIds = $group->members()->whereIn('users.id', $participantIds)->pluck('users.id')->toArray();
                                                        $hasPending = false;
                                                        if(!empty($memberIds)){
                                                            $hasPending = \App\Models\PblValidation::where('problem_id', $problem->id)->whereIn('user_id', $memberIds)->where('step', $stage)->where('status', 'pending')->exists()
                                                                || \App\Models\ProblemStageCompletion::where('problem_id', $problem->id)->whereIn('user_id', $memberIds)->where('stage', $stage)->where('status', 'pending')->exists();
                                                        }
                                                    @endphp
                                                    @if($hasPending)
                                                    <div class="flex gap-2 items-center">
                                                        <button type="button" class="group-validate inline-flex items-center px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700" data-member-ids="{{ json_encode($memberIds) }}" data-step="{{ $stage }}">Validasi Kelompok</button>
                                                        <button type="button" class="group-reject inline-flex items-center px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700" data-member-ids="{{ json_encode($memberIds) }}" data-step="{{ $stage }}">Tolak Kelompok</button>
                                                    </div>
                                                    @endif
                                                    @if($group->problem)
                                                        
                                                    @endif
                                                </div>
                                                @if(intval(request('stage', 1)) === 5)
                                                    <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded border border-gray-100 dark:border-gray-700 group-grade-panel">
                                                        <h4 class="font-semibold mb-2">Nilai Kelompok (Tahap 5)</h4>
                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                            <div>
                                                                <label class="text-xs font-medium">Kriteria 1 (20%)</label>
                                                                <input type="number" min="0" max="100" step="0.01" class="group-score-input c1 w-full rounded border-gray-200 p-2 text-sm" placeholder="0" />
                                                            </div>
                                                            <div>
                                                                <label class="text-xs font-medium">Kriteria 2 (40%)</label>
                                                                <input type="number" min="0" max="100" step="0.01" class="group-score-input c2 w-full rounded border-gray-200 p-2 text-sm" placeholder="0" />
                                                            </div>
                                                            <div>
                                                                <label class="text-xs font-medium">Kriteria 3 (25%)</label>
                                                                <input type="number" min="0" max="100" step="0.01" class="group-score-input c3 w-full rounded border-gray-200 p-2 text-sm" placeholder="0" />
                                                            </div>
                                                            <div>
                                                                <label class="text-xs font-medium">Kriteria 4 (15%)</label>
                                                                <input type="number" min="0" max="100" step="0.01" class="group-score-input c4 w-full rounded border-gray-200 p-2 text-sm" placeholder="0" />
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 flex items-center justify-between">
                                                            <div>Nilai akhir: <span class="group-final text-lg font-bold">-</span></div>
                                                            <div>
                                                                <button type="button" class="save-group-score inline-flex items-center px-3 py-1 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700" data-member-ids="{{ e(json_encode($memberIds)) }}" data-problem-id="{{ $problem->id }}">Simpan Nilai Kelompok</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                @else
                                                <div class="mt-2">
                                                @endif
                                                    <ul class="space-y-2">
                                                        @foreach($group->members()->whereIn('users.id', $participantIds)->get() as $member)
                                                            @php
                                                                $submission = $member->submissions()->where('problem_id', $problem->id)->first();
                                                                $stageText = $submission ? data_get($submission->stage_contents, $stage . '.text') : null;
                                                            @endphp
                                                            <li class="p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                                                <div class="font-medium">{{ $member->name }}</div>
                                                                <div class="text-xs text-gray-500 mt-1">Nilai akhir: <span id="final-member-{{ $member->id }}">{{ optional($submission)->nilai ?? '-' }}</span></div>
                                                                @if(intval($stage) === 5)
                                                                    @php $scAll = $submission ? ($submission->stage_contents ?? []) : []; @endphp
                                                                    @for($st=1;$st<=5;$st++)
                                                                        <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded text-sm text-gray-700 dark:text-gray-300">
                                                                            <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1">Jawaban / Feedback (Tahap {{ $st }})</div>
                                                                            <div class="whitespace-pre-line text-sm">{!! nl2br(e(data_get($scAll, $st . '.text', 'Belum mengisi untuk tahap ini.'))) !!}</div>
                                                                            @php $files = data_get($scAll, $st . '.files', []); @endphp
                                                                            @if(!empty($files) && is_array($files))
                                                                                <div class="mt-2 text-sm">
                                                                                    <div class="font-semibold text-xs">File:</div>
                                                                                    <ul class="list-disc pl-5">
                                                                                        @foreach($files as $idx => $p)
                                                                                            <li><a href="{{ route('submissions.file', ['submission' => $submission ? $submission->id : 0]) }}?stage={{ $st }}&index={{ $idx }}" class="text-indigo-600 hover:underline" target="_blank">{{ basename($p) }}</a></li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endfor
                                                                @else
                                                                    @if($stageText)
                                                                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{!! nl2br(e($stageText)) !!}</div>
                                                                    @else
                                                                        <div class="text-xs text-gray-500">Belum mengisi untuk tahap ini.</div>
                                                                    @endif
                                                                    @php $stageContents = $submission ? data_get($submission->stage_contents, $stage . '.files', []) : []; @endphp
                                                                    @if(!empty($stageContents) && is_array($stageContents))
                                                                        <div class="mt-2 text-sm">
                                                                            <div class="font-semibold text-xs">File:</div>
                                                                            <ul class="list-disc pl-5">
                                                                                @foreach($stageContents as $idx => $p)
                                                                                    <li><a href="{{ route('submissions.file', ['submission' => $submission ? $submission->id : 0]) }}?stage={{ $stage }}&index={{ $idx }}" class="text-indigo-600 hover:underline" target="_blank">{{ basename($p) }}</a></li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                                <!-- Teacher feedback for this member (group view) -->
                                                                <div class="mt-3 teacher-feedback-section" data-user-id="{{ $member->id }}">
                                                                    <div class="teacher-feedback-display">
                                                                        @php $msub = $member->submissions()->where('problem_id', $problem->id)->first(); @endphp
                                                                        @if(!empty(optional($msub)->feedback))
                                                                            <div class="mb-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded text-sm text-gray-800 dark:text-gray-200">
                                                                                <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1">Feedback Guru (tersimpan)</div>
                                                                                <div class="whitespace-pre-line text-sm">{{ optional($msub)->feedback }}</div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <form action="{{ url('/dosen/problems/'.$problem->id.'/student-feedback') }}" method="POST" class="teacher-feedback-form">
                                                                        @csrf
                                                                        <input type="hidden" name="user_id" value="{{ $member->id }}" />
                                                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Feedback Guru (opsional)</label>
                                                                        <textarea name="feedback" rows="2" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm p-2" placeholder="Tulis feedback singkat untuk siswa...">{{ optional($msub)->feedback }}</textarea>
                                                                        <div class="mt-2 flex justify-end">
                                                                            <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded">Simpan Feedback</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                            <div class="space-y-4">
                                @foreach($students as $student)
                                    <div class="border rounded p-4 bg-white dark:bg-gray-800">
                                        <div class="mb-3">
                                            <div class="font-medium">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $student->email ?? '' }}</div>
                                        </div>

                                        @php
                                            $stageText = null;
                                            if(!empty($student->submission)){
                                                $sc = $student->submission->stage_contents ?? [];
                                                $stageText = data_get($sc, $currentStage . '.text') ?? $student->submission->content ?? null;
                                            }
                                        @endphp
                                        @if(!empty($stageText))
                                            <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded text-sm text-gray-700 dark:text-gray-300">
                                                <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1">Feedback / Jawaban Singkat (Tahap {{ $currentStage }})</div>
                                                <div class="whitespace-pre-line text-sm">{{ $stageText }}</div>
                                                @if(!empty(optional($student->submission)->submitted_at))
                                                    <div class="text-xs text-gray-500 mt-2">Dikirim: {{ \Carbon\Carbon::parse($student->submission->submitted_at)->diffForHumans() }}</div>
                                                @endif
                                            </div>
                                        @endif

                                        @php
                                            $submissionObj = $student->submission ?? null;
                                            $stageFiles = $submissionObj ? data_get($submissionObj->stage_contents, $currentStage . '.files', []) : [];
                                        @endphp
                                        @if($submissionObj && !empty($stageFiles) && is_array($stageFiles))
                                            <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded text-sm border border-gray-100 dark:border-gray-700">
                                                <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-2">File Unggahan Siswa (Tahap {{ $currentStage }})</div>
                                                <ul class="list-disc pl-5 space-y-1">
                                                    @foreach($stageFiles as $idx => $path)
                                                        @php $name = basename($path); @endphp
                                                        <li>
                                                            <a href="{{ route('submissions.file', ['submission' => $submissionObj->id]) }}?stage={{ $currentStage }}&index={{ $idx }}" target="_blank" class="text-indigo-600 hover:underline">{{ $name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <!-- Teacher optional feedback display + form -->
                                        <div class="mt-3 teacher-feedback-section" data-user-id="{{ $student->id }}">
                                            <div class="teacher-feedback-display">
                                                @if(!empty(optional($student->submission)->feedback))
                                                    <div class="mb-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded text-sm text-gray-800 dark:text-gray-200">
                                                        <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1">Feedback Guru (tersimpan)</div>
                                                        <div class="whitespace-pre-line text-sm">{{ optional($student->submission)->feedback }}</div>
                                                    </div>
                                                @endif
                                            </div>

                                            <form action="{{ url('/dosen/problems/'.$problem->id.'/student-feedback') }}" method="POST" class="teacher-feedback-form">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Feedback Guru (opsional)</label>
                                                <textarea name="feedback" rows="2" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm p-2" placeholder="Tulis feedback singkat untuk siswa...">{{ optional($student->submission)->feedback }}</textarea>
                                                <div class="mt-2 flex justify-end">
                                                    <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded">Simpan Feedback</button>
                                                </div>
                                            </form>
                                        </div>

                                        @if(request()->has('stage') && intval(request('stage')) === 5)
                                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                                <h4 class="font-semibold mb-2">Refleksi & Ringkasan Tahap (1-5)</h4>
                                                        @php
                                                            $sc = $student->submission->stage_contents ?? [];
                                                        @endphp
                                                        @for($st=1;$st<=5;$st++)
                                                    <div class="mb-3 border-b pb-2">
                                                        <div class="text-sm font-semibold">Tahap {{ $st }}</div>
                                                        <div class="text-sm text-gray-700 mt-1">
                                                                    {!! nl2br(e(data_get($sc, $st . '.text', '—')) ) !!}
                                                        </div>
                                                        @php $files = data_get($sc, $st . '.files', []); @endphp
                                                        @if(!empty($files) && is_array($files))
                                                            <div class="mt-2 text-sm">
                                                                <div class="font-semibold text-xs">File:</div>
                                                                <ul class="list-disc pl-5">
                                                                    @foreach($files as $idx => $p)
                                                                        <li><a href="{{ route('submissions.file', ['submission' => $student->submission->id]) }}?stage={{ $st }}&index={{ $idx }}" class="text-indigo-600 hover:underline" target="_blank">{{ basename($p) }}</a></li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                        @endfor

                                                        @php $reflectionInstructions = data_get($problem->instructions, 5, []); @endphp
                                                        @if(!empty($reflectionInstructions) && is_array($reflectionInstructions))
                                                            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                                                <h4 class="font-semibold mb-2">Pertanyaan Refleksi</h4>
                                                                <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                                                    @foreach($reflectionInstructions as $ridx => $rinst)
                                                                                <li class="flex justify-between items-start">
                                                                                    <div class="mr-3">{!! nl2br(e(data_get($rinst,'text') ?? $rinst)) !!} @if(!empty(data_get($rinst,'created_at'))) <span class="text-xs text-gray-400">— {{ data_get($rinst,'created_at') }}</span>@endif</div>
                                                                                    <div class="flex items-center gap-2">
                                                                                        <button data-stage="5" data-index="{{ $ridx }}" class="edit-instruction text-xs text-indigo-600 hover:underline">Edit</button>
                                                                                        <button data-stage="5" data-index="{{ $ridx }}" class="delete-instruction text-xs text-red-600 hover:underline">Hapus</button>
                                                                                    </div>
                                                                                </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif

                                                        <div class="mt-4">
                                                    <h4 class="font-semibold mb-2">Evaluasi Kriteria (4 kriteria)</h4>
                                                    @php $scores = data_get($student->submission->stage_contents ?? [], '5.scores', []); $final = data_get($student->submission->stage_contents ?? [], '5.final'); @endphp
                                                    <form method="POST" action="{{ url('/dosen/problems/'.$problem->id.'/student-grade') }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                                            @for($k=1;$k<=4;$k++)
                                                                @php $ck = 'c'.$k; @endphp
                                                                <div>
                                                                    <label class="text-xs font-medium">Kriteria {{ $k }}</label>
                                                                    <input type="number" min="0" max="100" step="0.01" name="{{ $ck }}" value="{{ isset($scores[$ck]) ? $scores[$ck] : '' }}" class="w-full rounded border-gray-200 p-2 text-sm score-input score-{{ $student->id }}" />
                                                                </div>
                                                            @endfor
                                                        </div>
                                                        <div class="mt-3">
                                                            <label class="text-xs font-medium">Catatan (opsional)</label>
                                                            <textarea name="feedback" rows="2" class="w-full rounded border-gray-200 p-2 text-sm">{{ optional($student->submission)->feedback }}</textarea>
                                                        </div>
                                                        <div class="mt-3 flex justify-between items-center">
                                                            <div class="text-sm">Nilai akhir saat ini: <span id="final-{{ $student->id }}" class="font-semibold">{{ $final ?? ($student->submission->nilai ?? '-') }}</span></div>
                                                            <div>
                                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded">Simpan Evaluasi</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <script>
                                                (function(){
                                                    // compute average live for this student
                                                    const inputs = document.querySelectorAll('.score-{{ $student->id }}');
                                                    const out = document.getElementById('final-{{ $student->id }}');
                                                    if(!out) return;
                                                    function compute(){
                                                        let sum=0, cnt=0;
                                                        inputs.forEach(i=>{ const v=parseFloat(i.value); if(!isNaN(v)){ sum+=v; cnt++; }});
                                                        const avg = cnt? (Math.round((sum/cnt)*100)/100) : '-';
                                                        out.textContent = avg;
                                                    }
                                                    inputs.forEach(i=>i.addEventListener('input', compute));
                                                    compute();
                                                })();
                                            </script>
                                        @endif

                                        @php
                                            $pendingList = collect($student->pending_validations ?? collect())->merge($student->pending_stage_completions ?? collect());
                                        @endphp

                                        @if($pendingList && $pendingList->isNotEmpty())
                                            <div class="mt-4">
                                                @foreach($pendingList as $pending)
                                                    @php $step = data_get($pending, 'step') ?? data_get($pending, 'stage'); @endphp
                                                    <div class="mb-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded flex items-center justify-between">
                                                        <div class="text-sm text-yellow-800 dark:text-yellow-200">Menunggu Validasi Langkah {{ $step }}</div>
                                                        <div class="flex gap-2">
                                                            <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" data-student-name="{{ $student->name }}" data-step="{{ $step }}" data-action="approve" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                                <input type="hidden" name="step" value="{{ $step }}" />
                                                                <input type="hidden" name="action" value="approve" />
                                                                <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded">Validasi</button>
                                                            </form>
                                                            <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" data-student-name="{{ $student->name }}" data-step="{{ $step }}" data-action="reject" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                                <input type="hidden" name="step" value="{{ $step }}" />
                                                                <input type="hidden" name="action" value="reject" />
                                                                <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded">Tidak Valid</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="space-y-4">
                        @php
                            $deadline = $problem->deadline ? \Carbon\Carbon::parse($problem->deadline) : null;
                            $isPastDeadline = $deadline && $deadline->isPast();
                        @endphp

                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase text-gray-500">Tenggat Waktu</p>
                                    @if($deadline)
                                        <p class="mt-2 text-lg font-bold {{ $isPastDeadline ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $deadline->isoFormat('D MMMM YYYY') }}</p>
                                        <p class="text-sm {{ $isPastDeadline ? 'text-red-500' : 'text-gray-600' }}">Pukul {{ $deadline->format('H:i') }} WIB</p>
                                    @else
                                        <p class="mt-2 text-sm text-green-600 font-bold">Tanpa Tenggat Waktu</p>
                                    @endif
                                </div>
                                <div class="text-indigo-500">
                                    <x-heroicon-s-calendar-days class="w-10 h-10" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm font-semibold {{ $isPastDeadline ? 'text-red-600' : 'text-indigo-600' }}">
                                    @if($isPastDeadline)
                                        Tenggat sudah lewat
                                    @else
                                        Sisa waktu {{ $deadline ? $deadline->diffForHumans(['parts' => 2, 'short' => true]) : '' }}
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-3">
                                <a href="{{ url('/dosen/problem-based-learning') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white/60 text-indigo-700 font-semibold border border-gray-200 hover:bg-white">Kembali ke Daftar</a>
                                @if(request()->has('stage') && intval(request('stage')) === 5)
                                    <button id="add-reflection" data-problem-id="{{ $problem->id }}" class="inline-flex items-center justify-center mt-2 px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold border border-blue-700 hover:bg-blue-700">Tambah Pertanyaan Refleksi</button>
                                @endif
                            </div>
                            @if(request()->has('stage') && intval(request('stage')) === 5)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold mb-2">Rubrik Penilaian</h4>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 text-sm">
                                    <div class="overflow-x-auto">
                                        <table class="w-full table-auto text-xs">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-900">
                                                    <th class="px-2 py-1 text-left">No</th>
                                                    <th class="px-2 py-1 text-left">Kriteria Penilaian</th>
                                                    <th class="px-2 py-1 text-left">Bobot</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-t">
                                                    <td class="px-2 py-1 align-top">1</td>
                                                    <td class="px-2 py-1">Analisis Masalah &amp; Perancangan Logika (Menilai output PBL Sintaks 1 &amp; 2)<br><small class="text-gray-500">Mampu mengidentifikasi masalah, menyusun flowchart/pseudocode dan penamaan variabel</small>
                                                        <button type="button" class="ml-2 text-indigo-600 hover:underline text-xs rubric-toggle" data-target="rubric-desc-1" aria-expanded="false">Tampilkan</button>
                                                    </td>
                                                    <td class="px-2 py-1">20%</td>
                                                </tr>
                                                <tr id="rubric-desc-1" class="bg-gray-50 dark:bg-gray-900 text-sm hidden">
                                                    <td colspan="3" class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <strong>4-level:</strong>
                                                        <div class="mt-1">Sangat Baik: Mengidentifikasi masalah lengkap, desain logika jelas, flowchart/pseudocode akurat dan rapi.</div>
                                                        <div>Baik: Identifikasi dan desain baik, terdapat beberapa detail yang bisa disempurnakan.</div>
                                                        <div>Cukup: Identifikasi dasar sudah ada, desain sederhana dengan beberapa kekurangan.</div>
                                                        <div>Kurang: Identifikasi dan desain tidak jelas, banyak bagian yang belum lengkap.</div>
                                                    </td>
                                                </tr>

                                                <tr class="border-t">
                                                    <td class="px-2 py-1 align-top">2</td>
                                                    <td class="px-2 py-1">Kebenaran Sintaks &amp; Fungsionalitas Program (Menilai output PBL Sintaks 3)<br><small class="text-gray-500">Kompilasi dan akurasi output terhadap studi kasus</small>
                                                        <button type="button" class="ml-2 text-indigo-600 hover:underline text-xs rubric-toggle" data-target="rubric-desc-2" aria-expanded="false">Tampilkan</button>
                                                    </td>
                                                    <td class="px-2 py-1">40%</td>
                                                </tr>
                                                <tr id="rubric-desc-2" class="bg-gray-50 dark:bg-gray-900 text-sm hidden">
                                                    <td colspan="3" class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <strong>4-level:</strong>
                                                        <div class="mt-1">Sangat Baik: Program berjalan tanpa error, seluruh output sesuai studi kasus dan edge-case tercover.</div>
                                                        <div>Baik: Program berfungsi dengan baik, hanya beberapa kasus minor belum sempurna.</div>
                                                        <div>Cukup: Berfungsi untuk kasus umum, masih ada bug atau ketidaksesuaian di beberapa kasus.</div>
                                                        <div>Kurang: Program sering error atau output tidak sesuai studi kasus.</div>
                                                    </td>
                                                </tr>

                                                <tr class="border-t">
                                                    <td class="px-2 py-1 align-top">3</td>
                                                    <td class="px-2 py-1">Penerapan Konsep Materi  (Menilai output PBL Sintaks 3)<br><small class="text-gray-500">Kesesuaian penggunaan konsep (Class, Object, If-Else, dsb.)</small>
                                                        <button type="button" class="ml-2 text-indigo-600 hover:underline text-xs rubric-toggle" data-target="rubric-desc-3" aria-expanded="false">Tampilkan</button>
                                                    </td>
                                                    <td class="px-2 py-1">25%</td>
                                                </tr>
                                                <tr id="rubric-desc-3" class="bg-gray-50 dark:bg-gray-900 text-sm hidden">
                                                    <td colspan="3" class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <strong>4-level:</strong>
                                                        <div class="mt-1">Sangat Baik: Konsep materi diterapkan tepat dan efisien, penggunaan struktur/konsep sesuai konteks.</div>
                                                        <div>Baik: Konsep benar diterapkan namun ada area yang kurang optimal.</div>
                                                        <div>Cukup: Konsep terlihat tetapi terdapat kesalahan implementasi yang mempengaruhi hasil.</div>
                                                        <div>Kurang: Penerapan konsep tidak sesuai atau keliru sehingga hasil tidak memenuhi tujuan.</div>
                                                    </td>
                                                </tr>

                                                <tr class="border-t">
                                                    <td class="px-2 py-1 align-top">4</td>
                                                    <td class="px-2 py-1">Dokumentasi, Komunikasi &amp; Refleksi (Menilai output PBL Sintaks 4 &amp; 5)<br><small class="text-gray-500">Kelengkapan dokumentasi, kualitas refleksi dan kemampuan menjelaskan debugging</small>
                                                        <button type="button" class="ml-2 text-indigo-600 hover:underline text-xs rubric-toggle" data-target="rubric-desc-4" aria-expanded="false">Tampilkan</button>
                                                    </td>
                                                    <td class="px-2 py-1">15%</td>
                                                </tr>
                                                <tr id="rubric-desc-4" class="bg-gray-50 dark:bg-gray-900 text-sm hidden">
                                                    <td colspan="3" class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <strong>4-level:</strong>
                                                        <div class="mt-1">Sangat Baik: Dokumentasi lengkap, refleksi mendalam, mampu menjelaskan proses debugging dan pembelajaran.</div>
                                                        <div>Baik: Dokumentasi dan refleksi baik, tetapi kurang mendetail pada beberapa bagian.</div>
                                                        <div>Cukup: Dokumentasi terbatas dan refleksi singkat; penjelasan debugging minim.</div>
                                                        <div>Kurang: Dokumentasi atau refleksi tidak memadai atau tidak ada.</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3 text-xs text-gray-600">
                                        <strong>Skala:</strong> 4 = Sangat Baik, 3 = Baik, 2 = Cukup, 1 = Kurang
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('add-instruction');
            var modal = document.getElementById('add-instruction-modal');
            var modalTextarea = document.getElementById('new-instruction-text');
            var modalClose = document.getElementById('add-instruction-cancel');
            var modalSave = document.getElementById('add-instruction-save');
            if(btn && modal && modalTextarea && modalClose && modalSave){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    modalTextarea.value = '';
                    modalTextarea.focus();
                });

                modalClose.addEventListener('click', function(e){
                    e.preventDefault();
                    modal.classList.add('hidden');
                });

                modalSave.addEventListener('click', function(e){
                    e.preventDefault();
                    var id = btn.dataset.problemId;
                    var text = modalTextarea.value || '';
                    var stage = btn.dataset.stage || 2;
                    if(!text.trim()){
                        alert('Isi instruksi tidak boleh kosong.');
                        return;
                    }
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    modalSave.disabled = true;
                    fetch("{{ url('/dosen/problems') }}/"+id+"/instructions", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify({ instruction: text, stage: stage })
                    }).then(function(res){ return res.json(); }).then(function(json){
                        modalSave.disabled = false;
                        if(json && json.success){
                            modal.classList.add('hidden');
                            location.reload();
                        } else {
                            alert((json && json.message) ? json.message : 'Gagal menambah instruksi.');
                        }
                    }).catch(function(){ modalSave.disabled = false; alert('Gagal mengirim permintaan.'); });
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function(){
            var rbtn = document.getElementById('add-reflection');
            var rmodal = document.getElementById('add-reflection-modal');
            var rtextarea = document.getElementById('new-reflection-text');
            var rclose = document.getElementById('add-reflection-cancel');
            var rsave = document.getElementById('add-reflection-save');
            if(rbtn && rmodal && rtextarea && rclose && rsave){
                rbtn.addEventListener('click', function(e){
                    e.preventDefault();
                    rmodal.classList.remove('hidden');
                    rtextarea.value = '';
                    rtextarea.focus();
                });

                rclose.addEventListener('click', function(e){ e.preventDefault(); rmodal.classList.add('hidden'); });

                rsave.addEventListener('click', function(e){
                    e.preventDefault();
                    var id = rbtn.dataset.problemId;
                    var text = rtextarea.value || '';
                    if(!text.trim()){ alert('Isi pertanyaan refleksi tidak boleh kosong.'); return; }
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    rsave.disabled = true;
                    fetch("{{ url('/dosen/problems') }}/"+id+"/instructions", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body: JSON.stringify({ instruction: text, stage: 5 })
                    }).then(function(res){ return res.json(); }).then(function(json){
                        rsave.disabled = false;
                        if(json && json.success){ rmodal.classList.add('hidden'); location.reload(); } else { alert((json && json.message) ? json.message : 'Gagal menambah pertanyaan refleksi.'); }
                    }).catch(function(){ rsave.disabled = false; alert('Gagal mengirim permintaan.'); });
                });
            }
        });

        // Send feedback modal logic (top button)
        document.addEventListener('DOMContentLoaded', function(){
            var openBtn = document.getElementById('open-send-feedback-top');
            var modal = document.getElementById('send-feedback-modal');
            var cancel = document.getElementById('send-feedback-cancel');
            var save = document.getElementById('send-feedback-save');
            var userSelect = document.getElementById('send-feedback-user');
            var textarea = document.getElementById('send-feedback-text');
            if(openBtn && modal && cancel && save && userSelect && textarea){
                openBtn.addEventListener('click', function(e){ e.preventDefault(); modal.classList.remove('hidden'); textarea.value = ''; userSelect.focus(); });
                cancel.addEventListener('click', function(e){ e.preventDefault(); modal.classList.add('hidden'); });
                save.addEventListener('click', async function(e){
                    e.preventDefault();
                    var pid = openBtn.dataset.problemId;
                    var uid = userSelect.value;
                    var text = textarea.value || '';
                    if(!uid || !text.trim()){ alert('Pilih siswa dan isi feedback.'); return; }
                    save.disabled = true;
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    try{
                        var res = await fetch("{{ url('/dosen/problems') }}/"+pid+"/student-feedback", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: JSON.stringify({ user_id: uid, feedback: text })
                        });
                        var json = null; try{ json = await res.json(); }catch(err){}
                        save.disabled = false;
                        if(res.ok && json && (json.success || json.status === 'ok')){
                            // update DOM for the student (if present on page) instead of full reload
                            var section = document.querySelector('.teacher-feedback-section[data-user-id="'+uid+'"]');
                            if(section){
                                var display = section.querySelector('.teacher-feedback-display');
                                if(display){
                                    // build feedback block safely
                                    var wrapper = document.createElement('div');
                                    wrapper.className = 'mb-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded text-sm text-gray-800 dark:text-gray-200';
                                    var title = document.createElement('div'); title.className = 'font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1'; title.textContent = 'Feedback Guru (tersimpan)';
                                    var body = document.createElement('div'); body.className = 'whitespace-pre-line text-sm'; body.textContent = text;
                                    wrapper.appendChild(title); wrapper.appendChild(body);
                                    display.innerHTML = '';
                                    display.appendChild(wrapper);
                                }
                                // also update the per-student textarea value
                                var formTextarea = section.querySelector('textarea[name="feedback"]');
                                if(formTextarea) formTextarea.value = text;
                            }
                            modal.classList.add('hidden');
                            if(window.Swal) window.Swal.fire({title:'Berhasil', text:'Feedback terkirim.', icon:'success'});
                        }
                        else { alert((json && json.message) ? json.message : 'Gagal mengirim feedback.'); }
                    }catch(err){ save.disabled = false; alert('Gagal mengirim permintaan.'); }
                });
            }
        });

        // Group-level validation (approve/reject all members)
        document.addEventListener('DOMContentLoaded', function(){
            function confirmAndSend(memberIds, step, action){
                var title = action === 'approve' ? 'Validasi Kelompok' : 'Tolak Kelompok';
                var text = action === 'approve' ? 'Yakin ingin menyetujui semua anggota kelompok untuk langkah ' + step + '?' : 'Yakin ingin menolak semua anggota kelompok untuk langkah ' + step + '?';
                if(window.Swal){
                    return window.Swal.fire({title: title, text: text, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal'}).then(function(res){ return res.isConfirmed; });
                }
                return Promise.resolve(confirm(text));
            }

            async function handleGroupAction(e, action){
                e.preventDefault();
                var btn = e.currentTarget;
                var raw = btn.getAttribute('data-member-ids') || '[]';
                var memberIds = [];
                try{ memberIds = JSON.parse(raw); }catch(err){ memberIds = []; }
                var step = btn.getAttribute('data-step') || 1;
                if(!memberIds || !memberIds.length) return;
                var ok = await confirmAndSend(memberIds, step, action);
                if(!ok) return;

                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

                btn.disabled = true;
                var errors = [];
                for(var i=0;i<memberIds.length;i++){
                    var uid = memberIds[i];
                    try{
                        var res = await fetch("{{ url('/dosen/problems') }}/{{ $problem->id }}/pbl-validate", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: JSON.stringify({ user_id: uid, step: parseInt(step), action: action === 'approve' ? 'approve' : 'reject' })
                        });
                        if(!res.ok){
                            var txt = await res.text();
                            errors.push({user: uid, status: res.status, text: txt});
                        }
                    } catch(err){ errors.push({user: uid, error: err.message}); }
                }

                btn.disabled = false;
                if(errors.length){
                    var msg = 'Beberapa permintaan gagal. Periksa log.';
                    if(window.Swal) window.Swal.fire({title: 'Selesai', text: msg, icon: 'warning'});
                    else alert(msg);
                } else {
                    var msg = 'Aksi kelompok selesai.';
                    if(window.Swal) window.Swal.fire({title: 'Selesai', text: msg, icon: 'success'}).then(function(){ location.reload(); });
                    else { alert(msg); location.reload(); }
                }
            }

            document.querySelectorAll('.group-validate').forEach(function(b){ b.addEventListener('click', function(e){ handleGroupAction(e, 'approve'); }); });
            document.querySelectorAll('.group-reject').forEach(function(b){ b.addEventListener('click', function(e){ handleGroupAction(e, 'reject'); }); });
        });

        // Edit & Delete instruction handlers (with SweetAlert2 confirmation)
        document.addEventListener('DOMContentLoaded', function(){
            async function askConfirm(title, text, confirmText = 'Ya', icon = 'question'){
                if(window.Swal){
                    var res = await window.Swal.fire({ title: title, text: text, icon: icon, showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: 'Batal' });
                    return !!res.isConfirmed;
                }
                return confirm(text);
            }

            // Edit modal elements
            var editModal = document.getElementById('edit-instruction-modal');
            var editTextarea = document.getElementById('edit-instruction-text');
            var editCancel = document.getElementById('edit-instruction-cancel');
            var editSave = document.getElementById('edit-instruction-save');

            function openEditModal(stage, index, currentText){
                if(!editModal || !editTextarea) return;
                editModal.classList.remove('hidden');
                editTextarea.value = (currentText || '').trim();
                editTextarea.focus();
                if(editSave){ editSave.dataset.stage = stage; editSave.dataset.index = index; }
            }

            if(editCancel){ editCancel.addEventListener('click', function(e){ e.preventDefault(); if(editModal) editModal.classList.add('hidden'); }); }

            if(editSave){
                editSave.addEventListener('click', async function(e){
                    e.preventDefault();
                    var stage = editSave.dataset.stage;
                    var index = editSave.dataset.index;
                    var text = editTextarea.value || '';
                    if(!text.trim()){ alert('Isi instruksi tidak boleh kosong.'); return; }
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    editSave.disabled = true;
                    try{
                        var res = await fetch("{{ url('/dosen/problems') }}/{{ $problem->id }}/instructions/"+encodeURIComponent(stage)+"/"+encodeURIComponent(index), {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: JSON.stringify({ instruction: text })
                        });
                        var json = null;
                        try{ json = await res.json(); } catch(err){}
                        editSave.disabled = false;
                        if(res.ok && json && json.success){ if(editModal) editModal.classList.add('hidden'); location.reload(); }
                        else { alert((json && json.message) ? json.message : 'Gagal mengubah instruksi.'); }
                    } catch(err){ editSave.disabled = false; alert('Gagal mengirim permintaan.'); }
                });
            }

            // wire edit buttons: prefill with selected text when available
            document.querySelectorAll('.edit-instruction').forEach(function(b){
                b.addEventListener('click', function(e){
                    e.preventDefault();
                    var stage = b.getAttribute('data-stage');
                    var index = b.getAttribute('data-index');
                    var li = b.closest('li');
                    var textNode = '';

                    // prefer user-selected text if selection is within this instruction
                    try{
                        var selection = window.getSelection ? window.getSelection() : null;
                        var selText = selection ? (selection.toString() || '').trim() : '';
                        if(selText && selection.anchorNode && li && li.contains(selection.anchorNode)){
                            textNode = selText;
                        }
                    }catch(err){ textNode = ''; }

                    // fallback to instruction content if no selection
                    if(!textNode && li){
                        // find the content container (the div with class flex-1)
                        var contentDiv = li.querySelector('div.flex-1');
                        if(!contentDiv){
                            // fallback to a text-containing element
                            contentDiv = li.querySelector('div.text-sm') || li.querySelector('div');
                        }
                        if(contentDiv) textNode = (contentDiv.innerText || contentDiv.textContent || '').trim();
                        // strip created_at suffix if present (after em dash)
                        if(textNode.indexOf('—') !== -1) textNode = textNode.split('—')[0].trim();
                    }

                    openEditModal(stage, index, textNode);
                });
            });

            // delete handler using SweetAlert2 when available
            async function handleDelete(e){
                e.preventDefault();
                var btn = e.currentTarget;
                var stage = btn.getAttribute('data-stage');
                var index = btn.getAttribute('data-index');
                var ok = await askConfirm('Hapus Instruksi', 'Yakin ingin menghapus instruksi ini?', 'Hapus', 'warning');
                if(!ok) return;
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                btn.disabled = true;
                try{
                    var res = await fetch("{{ url('/dosen/problems') }}/{{ $problem->id }}/instructions/"+encodeURIComponent(stage)+"/"+encodeURIComponent(index), {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                    });
                    if(res.ok){
                        if(window.Swal) window.Swal.fire({ title: 'Terhapus', text: 'Instruksi berhasil dihapus.', icon: 'success' }).then(function(){ location.reload(); });
                        else location.reload();
                    } else {
                        var txt = await res.text();
                        if(window.Swal) window.Swal.fire({ title: 'Gagal', text: 'Gagal menghapus instruksi.', icon: 'error' });
                        else alert('Gagal menghapus instruksi.');
                        btn.disabled = false;
                    }
                } catch(err){ alert('Gagal mengirim permintaan.'); btn.disabled = false; }
            }

            document.querySelectorAll('.delete-instruction').forEach(function(b){ b.addEventListener('click', handleDelete); });
        });

        (function(){
            function setupSweetConfirm(){
                var forms = document.querySelectorAll('.pbl-validate-form');
                forms.forEach(function(form){
                    form.addEventListener('submit', function(e){
                        e.preventDefault();
                        var action = form.dataset.action || (form.querySelector('input[name="action"]') ? form.querySelector('input[name="action"]').value : '');
                        var student = form.dataset.studentName || '';
                        var step = form.dataset.step || '';
                        var title = action === 'approve' ? 'Konfirmasi Validasi' : (action === 'reject' ? 'Konfirmasi Penolakan' : 'Konfirmasi');
                        var text = action === 'approve' ? ('Yakin ingin menyetujui langkah ' + step + ' untuk siswa "' + student + '"?') : ('Yakin ingin menolak langkah ' + step + ' untuk siswa "' + student + '"?');

                        window.Swal.fire({
                            title: title,
                            text: text,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Batal'
                        }).then(function(result){
                            if(result.isConfirmed){
                                form.submit();
                            }
                        });
                    });
                });
            }

            // Load SweetAlert2 if not present, then setup handlers
            document.addEventListener('DOMContentLoaded', function(){
                if(window.Swal) return setupSweetConfirm();
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.onload = setupSweetConfirm;
                document.head.appendChild(s);
            });
        })();
    </script>
    @if(session('success') || session('error'))
        <script>
            (function(){
                function showMsg(){
                    var type = {!! json_encode(session('success') ? 'success' : 'error') !!};
                    var text = {!! json_encode(session('success') ?? session('error')) !!};
                    if(window.Swal){
                        window.Swal.fire({title: type === 'success' ? 'Berhasil' : 'Gagal', text: text, icon: type});
                    } else {
                        var s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                        s.onload = function(){ window.Swal.fire({title: type === 'success' ? 'Berhasil' : 'Gagal', text: text, icon: type}); };
                        document.head.appendChild(s);
                    }
                }
                document.addEventListener('DOMContentLoaded', showMsg);
            })();
        </script>
    @endif
    <!-- Add Instruction Modal -->
    <div id="add-instruction-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-3">Tambah Instruksi</h3>
            <textarea id="new-instruction-text" rows="5" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm p-3" placeholder="Tulis instruksi untuk siswa..."></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button id="add-instruction-cancel" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button id="add-instruction-save" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Edit Instruction Modal -->
    <div id="edit-instruction-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-3">Edit Instruksi</h3>
            <textarea id="edit-instruction-text" rows="5" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm p-3" placeholder="Ubah instruksi..."></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button id="edit-instruction-cancel" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button id="edit-instruction-save" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Add Reflection Modal -->
    <div id="add-reflection-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-3">Tambah Pertanyaan Refleksi</h3>
            <textarea id="new-reflection-text" rows="5" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm p-3" placeholder="Tulis pertanyaan refleksi untuk siswa..."></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button id="add-reflection-cancel" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button id="add-reflection-save" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Send Feedback Modal (top) -->
    <div id="send-feedback-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-3">Kirim Feedback ke Siswa</h3>
            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Pilih Siswa</label>
            <select id="send-feedback-user" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm p-2 mb-2">
                @foreach($students as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}{{ $s->email ? ' (' . $s->email . ')' : '' }}</option>
                @endforeach
            </select>
            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Feedback</label>
            <textarea id="send-feedback-text" rows="4" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm p-3" placeholder="Tulis feedback untuk siswa..."></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button id="send-feedback-cancel" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button id="send-feedback-save" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Kirim Feedback</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.rubric-toggle').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var targetId = btn.dataset.target;
                    if(!targetId) return;
                    var el = document.getElementById(targetId);
                    if(!el) return;
                    var isHidden = el.classList.contains('hidden');
                    if(isHidden){
                        el.classList.remove('hidden');
                        btn.textContent = 'Sembunyikan';
                        btn.setAttribute('aria-expanded','true');
                    } else {
                        el.classList.add('hidden');
                        btn.textContent = 'Tampilkan';
                        btn.setAttribute('aria-expanded','false');
                    }
                });
            });
        });
    </script>
    <script>
        // Delegated handler for robust Save Nilai Kelompok clicks
        document.addEventListener('click', async function(e){
            var btn = e.target.closest('.save-group-score');
            if(!btn) return;
            e.preventDefault();
            try{
                console.log('Save group score clicked');
                var panel = btn.closest('.group-grade-panel');
                if(!panel){ alert('Panel penilaian tidak ditemukan.'); return; }
                var c1 = panel.querySelector('.c1');
                var c2 = panel.querySelector('.c2');
                var c3 = panel.querySelector('.c3');
                var c4 = panel.querySelector('.c4');
                var n1 = c1 ? parseFloat(c1.value) || 0 : 0;
                var n2 = c2 ? parseFloat(c2.value) || 0 : 0;
                var n3 = c3 ? parseFloat(c3.value) || 0 : 0;
                var n4 = c4 ? parseFloat(c4.value) || 0 : 0;
                var final = Math.round(((n1*0.2)+(n2*0.4)+(n3*0.25)+(n4*0.15))*100)/100;
                var out = panel.querySelector('.group-final'); if(out) out.textContent = final;

                var memberRaw = btn.getAttribute('data-member-ids') || '[]';
                var memberIds = [];
                try{ memberIds = JSON.parse(memberRaw); } catch(err){ memberIds = []; }
                if(!memberIds.length){ alert('Tidak ada anggota untuk dinilai.'); return; }

                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                var problemId = btn.getAttribute('data-problem-id');

                if(window.Swal){
                    var conf = await window.Swal.fire({ title: 'Simpan Nilai Kelompok', text: 'Simpan nilai untuk semua anggota kelompok?', icon: 'question', showCancelButton: true, confirmButtonText: 'Simpan', cancelButtonText: 'Batal' });
                    if(!conf.isConfirmed) return;
                } else if(!confirm('Simpan nilai untuk semua anggota kelompok?')) return;

                btn.disabled = true;
                var errs = [];
                for(var i=0;i<memberIds.length;i++){
                    var uid = memberIds[i];
                    var form = new FormData();
                    form.append('_token', token);
                    form.append('user_id', uid);
                    form.append('c1', n1);
                    form.append('c2', n2);
                    form.append('c3', n3);
                    form.append('c4', n4);
                    try{
                        var res = await fetch("{{ url('/dosen/problems') }}/"+problemId+"/student-grade", { method: 'POST', body: form });
                        if(!res.ok) errs.push(uid);
                        else {
                            var finalEl = document.getElementById('final-member-' + uid);
                            if(finalEl) finalEl.textContent = final;
                        }
                    } catch(err){ errs.push(uid); }
                }
                btn.disabled = false;
                if(errs.length){
                    var msg = 'Beberapa penyimpanan nilai gagal untuk anggota: ' + errs.join(', ');
                    if(window.Swal) window.Swal.fire({ title: 'Selesai', text: msg, icon: 'warning' }); else alert(msg);
                } else {
                    var msg = 'Nilai kelompok berhasil disimpan.';
                    if(window.Swal) window.Swal.fire({ title: 'Sukses', text: msg, icon: 'success' }).then(()=> location.reload()); else { alert(msg); location.reload(); }
                }
            } catch(err){ console.error(err); alert('Terjadi kesalahan: ' + (err.message || err)); }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // compute weighted final for group panels
            document.querySelectorAll('.group-grade-panel').forEach(function(panel){
                var c1 = panel.querySelector('.c1');
                var c2 = panel.querySelector('.c2');
                var c3 = panel.querySelector('.c3');
                var c4 = panel.querySelector('.c4');
                var out = panel.querySelector('.group-final');
                function compute(){
                    var n1 = parseFloat(c1.value) || 0;
                    var n2 = parseFloat(c2.value) || 0;
                    var n3 = parseFloat(c3.value) || 0;
                    var n4 = parseFloat(c4.value) || 0;
                    var final = (n1 * 0.2) + (n2 * 0.4) + (n3 * 0.25) + (n4 * 0.15);
                    out.textContent = isNaN(final) ? '-' : (Math.round(final * 100) / 100);
                }
                [c1,c2,c3,c4].forEach(function(i){ if(i) i.addEventListener('input', compute); });

                var saveBtn = panel.querySelector('.save-group-score');
                if(!saveBtn) return;
                saveBtn.addEventListener('click', async function(e){
                    e.preventDefault();
                    var memberRaw = saveBtn.getAttribute('data-member-ids') || '[]';
                    var memberIds = [];
                    try{ memberIds = JSON.parse(memberRaw); } catch(err){ memberIds = []; }
                    if(!memberIds.length){ alert('Tidak ada anggota untuk dinilai.'); return; }
                    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                    var payload = { c1: c1.value || '', c2: c2.value || '', c3: c3.value || '', c4: c4.value || '' };
                    var problemId = saveBtn.getAttribute('data-problem-id');
                    if(window.Swal){
                        var conf = await window.Swal.fire({ title: 'Simpan Nilai Kelompok', text: 'Simpan nilai untuk semua anggota kelompok?', icon: 'question', showCancelButton: true, confirmButtonText: 'Simpan', cancelButtonText: 'Batal' });
                        if(!conf.isConfirmed) return;
                    } else if(!confirm('Simpan nilai untuk semua anggota kelompok?')) return;

                    saveBtn.disabled = true;
                    var errors = [];
                    for(var i=0;i<memberIds.length;i++){
                        var uid = memberIds[i];
                        try{
                            var form = new FormData();
                            form.append('_token', token);
                            form.append('user_id', uid);
                            form.append('c1', payload.c1);
                            form.append('c2', payload.c2);
                            form.append('c3', payload.c3);
                            form.append('c4', payload.c4);
                            var res = await fetch("{{ url('/dosen/problems') }}/"+problemId+"/student-grade", { method: 'POST', body: form });
                            if(!res.ok) errors.push(uid);
                            else {
                                // update per-member final display
                                var finalEl = document.getElementById('final-member-' + uid);
                                if(finalEl) finalEl.textContent = (Math.round(((parseFloat(payload.c1||0)*0.2)+(parseFloat(payload.c2||0)*0.4)+(parseFloat(payload.c3||0)*0.25)+(parseFloat(payload.c4||0)*0.15))*100)/100;
                            }
                        } catch(err){ errors.push(uid); }
                    }
                    saveBtn.disabled = false;
                    if(errors.length){
                        var msg = 'Beberapa penyimpanan nilai gagal untuk anggota: ' + errors.join(', ');
                        if(window.Swal) window.Swal.fire({ title: 'Selesai', text: msg, icon: 'warning' });
                        else alert(msg);
                    } else {
                        var msg = 'Nilai kelompok berhasil disimpan.';
                        if(window.Swal) window.Swal.fire({ title: 'Sukses', text: msg, icon: 'success' }).then(()=> location.reload());
                        else { alert(msg); location.reload(); }
                    }
                });
            });
        });
    </script>
    @endif
</x-app-layout>
