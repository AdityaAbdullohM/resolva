<x-app-layout>
    

    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <a href="{{ (request()->has('stage') && intval(request('stage')) >= 1 && intval(request('stage')) <= 5) ? url('/mahasiswa/problem-based-learning') : url()->previous() }}" class="inline-flex items-center text-indigo-200 hover:text-white mb-2">
                            &larr; Kembali
                        </a>
                        <h1 class="text-3xl font-extrabold tracking-tight">{{ $problem->judul }}</h1>
                        @php
                            $pblNames = [
                                '1. Orientasi pada masalah',
                                '2. Mengorganisasikan Peserta Didik untuk belajar',
                                '3. Penyelidikan individu',
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
                        
                        @if(intval(request('stage', 1)) !== 5 && intval(request('stage', 1)) !== 1)
                        <!-- Instruksi (swapped) -->
                        <div class="mt-6 border-t pt-5">
                            @php
                                $currentStage = intval(request('stage', 1));
                                // default show stage 2 instructions, but for stage 3 or 4 show that stage's instructions
                                $displayStage = in_array($currentStage, [3,4]) ? $currentStage : 2;
                                $stageInstructions = data_get($problem->instructions, $displayStage, []);
                                $heading = 'Instruksi Belajar';
                            @endphp
                            <h3 class="text-lg font-semibold mb-3">{{ $heading }}</h3>
                            @if(!empty($stageInstructions) && is_array($stageInstructions))
                                <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                    <ul class="space-y-3">
                                        @foreach($stageInstructions as $idx => $inst)
                                            <li class="flex items-start gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                                <div class="flex-shrink-0">
                                                    <div class="w-9 h-9 rounded-full bg-indigo-500 text-white font-semibold flex items-center justify-center">{{ $idx + 1 }}</div>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="text-sm text-gray-800 dark:text-gray-100">{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!}</div>
                                                    @if(!empty(data_get($inst,'created_at')))
                                                        <div class="text-xs text-gray-400 mt-2">— {{ data_get($inst,'created_at') }}</div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">Belum ada {{ strtolower($heading) }} untuk tahap ini.</div>
                            @endif
                        </div>
                        @endif

                       

                        @if(request('stage') == 2)
                            <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Kirim Jawaban & File</h3>
                                @if(session('success'))
                                    <div class="mb-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
                                @endif
                                <form action="{{ route('mahasiswa.problems.submit.store', $problem) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="stage" value="2">
                                    <div class="mb-4">
                                        <label for="content_stage2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jawaban / Catatan (opsional)</label>
                                        @php
                                            $currentStage = 2;
                                            $stageContent = null;
                                            if(isset($submission) && !empty($submission->stage_contents) && is_array($submission->stage_contents)){
                                                $stageContent = data_get($submission->stage_contents, $currentStage . '.text');
                                            }
                                            $initialContent = old('content', $stageContent ?? (isset($submission) ? $submission->content : ''));
                                        @endphp
                                        <textarea name="content" id="content_stage2" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" placeholder="Tulis jawaban singkat atau catatan untuk dosen...">{{ $initialContent }}</textarea>
                                        @error('content')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="files_stage2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unggah File (opsional)</label>
                                        <input type="file" id="files_stage2" name="files[]" multiple class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, DOCX, ZIP, JAVA (Maks. 10MB per file)</p>
                                        @error('files')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Kirim Jawaban & File</button>
                                    </div>
                                </form>

                                @if(isset($submission))
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="text-sm font-semibold mb-2">Jawaban yang telah dikirim (Tahap 2)</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line mb-2">{!! nl2br(e(data_get($submission->stage_contents, '2.text') ?? '-')) !!}</div>
                                        @php
                                            $files = data_get($submission->stage_contents, '2.files') ?? [];
                                        @endphp
                                        @if(!empty($files))
                                            <div class="mt-2">
                                                <strong class="text-sm">File:</strong>
                                                <ul class="mt-1 space-y-1 text-sm">
                                                    @foreach((array) $files as $f)
                                                        @php
                                                            $filePath = is_string($f) ? $f : (data_get($f, 'file_path') ?? data_get($f, 'path'));
                                                            $originalName = is_string($f) ? basename($f) : (data_get($f, 'original_name') ?? basename($filePath ?? 'File'));
                                                        @endphp
                                                        <li><a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $originalName }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(request('stage') == 3)
                            <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Kirim Hasil Penyelidikan & File</h3>
                                @if(session('success'))
                                    <div class="mb-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
                                @endif
                                <form action="{{ route('mahasiswa.problems.submit.store', $problem) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="stage" value="3">
                                    <div class="mb-4">
                                        <label for="content_stage3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hasil Penyelidikan (opsional)</label>
                                        @php
                                            $currentStage = 3;
                                            $stageContent = null;
                                            if(isset($submission) && !empty($submission->stage_contents) && is_array($submission->stage_contents)){
                                                $stageContent = data_get($submission->stage_contents, $currentStage . '.text');
                                            }
                                            $initialContent = old('content', $stageContent ?? (isset($submission) ? $submission->content : ''));
                                        @endphp
                                        <textarea name="content" id="content_stage3" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" placeholder="Tulis jawaban singkat atau catatan untuk dosen...">{{ $initialContent }}</textarea>
                                        @error('content')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="files_stage3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unggah File (opsional)</label>
                                        <input type="file" id="files_stage3" name="files[]" multiple class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, DOCX, ZIP, JAVA (Maks. 10MB per file)</p>
                                        @error('files')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Kirim Hasil & File</button>
                                    </div>
                                </form>

                                @if(isset($submission))
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="text-sm font-semibold mb-2">Hasil Penyelidikan yang telah dikirim (Tahap 3)</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line mb-2">{!! nl2br(e(data_get($submission->stage_contents, '3.text') ?? '-')) !!}</div>
                                        @php
                                            $files = data_get($submission->stage_contents, '3.files') ?? [];
                                        @endphp
                                        @if(!empty($files))
                                            <div class="mt-2">
                                                <strong class="text-sm">File:</strong>
                                                <ul class="mt-1 space-y-1 text-sm">
                                                    @foreach((array) $files as $f)
                                                        @php
                                                            $filePath = is_string($f) ? $f : (data_get($f, 'file_path') ?? data_get($f, 'path'));
                                                            $originalName = is_string($f) ? basename($f) : (data_get($f, 'original_name') ?? basename($filePath ?? 'File'));
                                                        @endphp
                                                        <li><a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $originalName }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(request('stage') == 4)
                            <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Kirim Karya Tugas & File</h3>
                                @if(session('success'))
                                    <div class="mb-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
                                @endif
                                <form action="{{ route('mahasiswa.problems.submit.store', $problem) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="stage" value="4">
                                    <div class="mb-4">
                                        <label for="content_stage4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Penjelasan Karya (opsional)</label>
                                        @php
                                            $currentStage = 4;
                                            $stageContent = null;
                                            if(isset($submission) && !empty($submission->stage_contents) && is_array($submission->stage_contents)){
                                                $stageContent = data_get($submission->stage_contents, $currentStage . '.text');
                                            }
                                            $initialContent = old('content', $stageContent ?? (isset($submission) ? $submission->content : ''));
                                        @endphp
                                        <textarea name="content" id="content_stage4" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" placeholder="Tulis jawaban singkat atau catatan untuk dosen...">{{ $initialContent }}</textarea>
                                        @error('content')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="files_stage4" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unggah Karya (opsional)</label>
                                        <input type="file" id="files_stage4" name="files[]" multiple class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, DOCX, ZIP, JAVA (Maks. 10MB per file)</p>
                                        @error('files')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Kirim Karya & File</button>
                                    </div>
                                </form>

                                @if(isset($submission))
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="text-sm font-semibold mb-2">Karya Tugas yang telah dikirim (Tahap 4)</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line mb-2">{!! nl2br(e(data_get($submission->stage_contents, '4.text') ?? '-')) !!}</div>
                                        @php
                                            $files = data_get($submission->stage_contents, '4.files') ?? [];
                                        @endphp
                                        @if(!empty($files))
                                            <div class="mt-2">
                                                <strong class="text-sm">File:</strong>
                                                <ul class="mt-1 space-y-1 text-sm">
                                                    @foreach((array) $files as $f)
                                                        @php
                                                            $filePath = is_string($f) ? $f : (data_get($f, 'file_path') ?? data_get($f, 'path'));
                                                            $originalName = is_string($f) ? basename($f) : (data_get($f, 'original_name') ?? basename($filePath ?? 'File'));
                                                        @endphp
                                                        <li><a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $originalName }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(request('stage') == 1)
                            <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Kirim Jawaban & File</h3>
                                @if(session('success'))
                                    <div class="mb-3 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div>
                                @endif
                                <form action="{{ route('mahasiswa.problems.submit.store', $problem) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="stage" value="1">
                                    <div class="mb-4">
                                        <label for="content_stage1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jawaban Anda (opsional)</label>
                                        @php
                                            $currentStage = 1;
                                            $stageContent = null;
                                            if(isset($submission) && !empty($submission->stage_contents) && is_array($submission->stage_contents)){
                                                $stageContent = data_get($submission->stage_contents, $currentStage . '.text');
                                            }
                                            $initialContent = old('content', $stageContent ?? (isset($submission) ? $submission->content : ''));
                                        @endphp
                                        <textarea name="content" id="content_stage1" rows="4" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" placeholder="Tulis jawaban Anda di sini...">{{ $initialContent }}</textarea>
                                        @error('content')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="files_stage1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unggah File (opsional)</label>
                                        <input type="file" id="files_stage1" name="files[]" multiple class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, DOCX, ZIP, JAVA (Maks. 10MB per file)</p>
                                        @error('files')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Kirim Jawaban & File</button>
                                    </div>
                                </form>

                                @if(isset($submission))
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="text-sm font-semibold mb-2">Jawaban yang telah dikirim</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line mb-2">{!! nl2br(e(data_get($submission->stage_contents, '1.text') ?? $submission->content ?? '-')) !!}</div>
                                        @php
                                            $files = data_get($submission->stage_contents, '1.files') ?? data_get($submission, 'files') ?? null;
                                        @endphp
                                        @if(!empty($files))
                                            <div class="mt-2">
                                                <strong class="text-sm">File:</strong>
                                                <ul class="mt-1 space-y-1 text-sm">
                                                    @foreach((array) $files as $f)
                                                        @php
                                                            $filePath = data_get($f, 'file_path') ?? data_get($f, 'path') ?? $f;
                                                            $originalName = data_get($f, 'original_name') ?? data_get($f, 'originalName') ?? (is_string($f) ? basename($f) : 'File');
                                                        @endphp
                                                        <li><a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $originalName }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(request('stage') == 5)
                            <div class="mt-6 border-t pt-5">
                                <h3 class="text-lg font-semibold mb-3">Instruksi Refleksi & Evaluasi</h3>
                                @php
                                    $stage = 5;
                                    $stageInstructions = data_get($problem->instructions, $stage, []);
                                @endphp
                                @if(!empty($stageInstructions) && is_array($stageInstructions))
                                    <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg">
                                        <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                                @foreach($stageInstructions as $inst)
                                                    <li>{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!} @if(!empty(data_get($inst,'created_at'))) <span class="text-xs text-gray-400">— {{ data_get($inst,'created_at') }}</span>@endif</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">Belum ada instruksi refleksi untuk tahap ini.</div>
                                @endif

                                <div class="mt-4">
                                    <h4 class="text-sm font-semibold mb-2">Isi Refleksi Anda</h4>
                                    <form action="{{ route('mahasiswa.problems.feedback.store', $problem) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="stage" value="5">
                                        <div class="mb-3">
                                            <textarea name="content" rows="4" class="w-full rounded border-gray-200 p-2" placeholder="Tulis refleksi Anda di sini...">{{ old('content', data_get(optional($submission),'stage_contents.5.text') ?? '') }}</textarea>
                                        </div>
                                        
                                        <div class="flex justify-end">
                                            <button class="px-3 py-2 bg-indigo-600 text-white rounded">Kirim Refleksi</button>
                                        </div>
                                    </form>
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
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="space-y-4">
                        @php
                            $deadline = $problem->deadline ? \Carbon\Carbon::parse($problem->deadline) : null;
                            $isPastDeadline = $deadline && $deadline->isPast();
                            $hasSubmission = isset($submission) && $submission;
                            $submittedAt = $hasSubmission && $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at) : null;
                            $wasSubmittedLate = $deadline && $submittedAt && $submittedAt->greaterThan($deadline);
                            $wasSubmittedEarly = $deadline && $submittedAt && $submittedAt->lessThan($deadline);
                            $noSubmissionPastDeadline = !$hasSubmission && $isPastDeadline;
                            $lateVisible = $wasSubmittedLate || $noSubmissionPastDeadline;
                        @endphp

                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase text-gray-500">Tenggat Waktu</p>
                                    @if($deadline)
                                        <p class="mt-2 text-lg font-bold {{ $lateVisible ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $deadline->isoFormat('D MMMM YYYY') }}</p>
                                        <p class="text-sm {{ $lateVisible ? 'text-red-500' : 'text-gray-600' }}">Pukul {{ $deadline->format('H:i') }} WIB</p>
                                    @else
                                        <p class="mt-2 text-sm text-green-600 font-bold">Tanpa Tenggat Waktu</p>
                                    @endif
                                </div>
                                <div class="text-indigo-500">
                                    <x-heroicon-s-calendar-days class="w-10 h-10" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm font-semibold {{ $lateVisible ? 'text-red-600' : ($wasSubmittedEarly ? 'text-green-600' : 'text-indigo-600') }}">
                                    @if($wasSubmittedLate)
                                        Terlambat {{ $submittedAt->diffForHumans($deadline, ['parts' => 2, 'short' => true]) }}
                                    @elseif($wasSubmittedEarly)
                                        Dikirim lebih cepat {{ $deadline->diffForHumans($submittedAt, ['parts' => 2, 'short' => true]) }}
                                    @elseif($noSubmissionPastDeadline)
                                        Terlambat {{ $deadline->diffForHumans(['parts' => 2, 'short' => true]) }}
                                    @else
                                        Sisa waktu {{ $deadline ? $deadline->diffForHumans(['parts' => 2, 'short' => true]) : '' }}
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-3">
                                
                                <a href="{{ url('/mahasiswa/problem-based-learning') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white/60 text-indigo-700 font-semibold border border-gray-200 hover:bg-white">Kembali ke Daftar</a>
                            </div>
                        </div>

                        
                        

                        @if(intval(request('stage', 1)) === 5 && isset($submission))
                            @php
                                $final = data_get($submission->stage_contents ?? [], '5.final');
                                $grade = $final ?? ($submission->nilai ?? null);
                                $teacherFeedback = $submission->feedback ?? null;
                            @endphp
                            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mt-4">
                                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-2">Hasil & Feedback Dosen</h3>
                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                    <strong>Nilai Akhir: </strong> {{ $grade !== null ? $grade : '-' }}
                                </div>
                                @if($teacherFeedback)
                                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                        <strong>Feedback dari Dosen</strong>
                                        <div class="mt-2">{!! nl2br(e($teacherFeedback)) !!}</div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">Belum ada feedback dari dosen.</div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@if(intval(request('stage', 1)) !== 5)
<!-- Upload Modal -->
<div id="upload-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl p-6">
        <h3 class="text-lg font-semibold mb-3">Unggah File Tugas</h3>
        <form id="upload-form" action="{{ route('mahasiswa.problems.submit.store', $problem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="stage" value="{{ request('stage', 1) }}">
            <div class="mb-4">
                <label for="upload-content" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Catatan / Pesan (opsional)</label>
                <textarea id="upload-content" name="content" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2"></textarea>
            </div>
            <div class="mb-4">
                <label for="upload-files" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Pilih file (boleh lebih dari satu)</label>
                <input id="upload-files" name="files[]" type="file" multiple class="block w-full text-sm text-gray-700">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="upload-cancel" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Batal</button>
                <button type="submit" id="upload-submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Unggah</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
            var openBtns = document.querySelectorAll('.open-upload-modal');
        var modal = document.getElementById('upload-modal');
        var cancel = document.getElementById('upload-cancel');
        openBtns.forEach(function(openBtn){
            openBtn.addEventListener('click', function(e){
                e.preventDefault();
                if(modal) modal.classList.remove('hidden');
            });
        });
        if(cancel && modal){
            cancel.addEventListener('click', function(e){ e.preventDefault(); modal.classList.add('hidden'); });
        }
        // close on overlay click
        if(modal){
            modal.addEventListener('click', function(e){ if(e.target === modal) modal.classList.add('hidden'); });
        }
    });
</script>
@endif
