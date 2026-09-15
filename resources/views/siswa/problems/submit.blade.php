<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8">
                <a href="{{ url('/mahasiswa/problem-based-learning') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200 bg-white dark:bg-gray-800 px-4 py-2 rounded-full shadow-sm border border-gray-200 dark:border-gray-700">
                    <x-heroicon-s-arrow-left class="w-4 h-4 mr-2" />
                    Kembali
                </a>
                <div class="mt-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            {{ isset($submission) ? 'Perbarui Tugas' : 'Kumpulkan Tugas' }}
                        </h1>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 flex items-center">
                            <x-heroicon-o-book-open class="w-5 h-5 mr-2 text-indigo-500" />
                            {{ $problem->judul }}
                        </p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-sm">
                            <x-heroicon-s-academic-cap class="w-5 h-5 mr-2" />
                            Kelas: {{ $problem->kelas->nama }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Left Column: Form -->
                <div class="lg:w-2/3">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                        <!-- Decorative top gradient -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
                        
                        <div class="p-6 sm:p-8 mt-2">
                            <form x-data="{ fileName: null, fileSize: null, showConfirm: false, isDragging: false }" 
                                  action="{{ isset($submission) ? route('mahasiswa.problems.submit.update', $problem) : route('mahasiswa.problems.submit.store', $problem) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data">
                                @csrf
                                @if(isset($submission))
                                    @method('PATCH')
                                @endif

                                <!-- Text Content Area -->
                                <div class="mb-8">
                                    <label for="content" class="block text-base font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Catatan / Pesan <span class="text-gray-400 font-normal text-sm">(Opsional)</span>
                                    </label>
                                    <div class="relative">
                                        <textarea name="content" id="content" rows="6" 
                                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 resize-y shadow-sm sm:text-base @error('content') border-red-500 ring-red-500 @enderror"
                                            placeholder="Tuliskan penjelasan tambahan untuk dosen Anda di sini...">{{ old('content', $submission->content ?? '') }}</textarea>
                                    </div>
                                    @error('content')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center"><x-heroicon-m-exclamation-circle class="w-4 h-4 mr-1"/>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- File Upload Area -->
                                <div class="mb-8">
                                    <label class="block text-base font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        File Tugas <span class="text-red-500">*</span>
                                    </label>
                                    
                                    @if(isset($submission) && $submission->file_path)
                                        <div class="mb-5 p-4 rounded-xl bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center">
                                                <div class="p-2.5 bg-blue-100 dark:bg-blue-800/60 rounded-lg mr-3 shadow-sm">
                                                    <x-heroicon-s-document-check class="w-6 h-6 text-blue-600 dark:text-blue-300" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">File Saat Ini</p>
                                                    @if(is_array($submission->file_path))
                                                        <div class="text-sm">
                                                            @foreach($submission->file_path as $i => $p)
                                                                <a href="{{ route('submissions.file', $submission) }}?index={{ $i }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">Unduh File {{ $i + 1 }}</a>@if(!$loop->last) | @endif
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <a href="{{ route('submissions.file', $submission) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">Unduh / Lihat File</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-100/50 dark:bg-blue-900/40 px-3 py-1.5 rounded-full inline-flex items-center w-max">
                                                <x-heroicon-s-check-circle class="w-4 h-4 mr-1" />
                                                Sudah Diunggah
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-1 relative flex flex-col justify-center px-6 pt-8 pb-8 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-2xl transition-all duration-200 bg-gray-50/50 dark:bg-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800"
                                         :class="{ 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20 shadow-inner': isDragging, 'hover:border-indigo-400': !isDragging }"
                                         @dragover.prevent="isDragging = true"
                                         @dragleave.prevent="isDragging = false"
                                         @drop.prevent="isDragging = false; const dt = $event.dataTransfer; if(dt.files.length) { $refs.fileInput.files = dt.files; $refs.fileInput.dispatchEvent(new Event('change')); }">
                                        
                                        <div class="space-y-3 text-center relative z-10" x-show="!fileName">
                                            <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center mb-4 transition-transform duration-300" :class="{ 'scale-110': isDragging }">
                                                <x-heroicon-o-cloud-arrow-up class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                                            </div>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center items-center">
                                                <label for="files" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 transition-colors">
                                                    <span>Klik untuk memilih file (bisa lebih dari satu)</span>
                                                    <input id="files" name="files[]" type="file" multiple class="sr-only" x-ref="fileInput"
                                                           x-on:change="(e)=>{ const f = e.target.files; if(f && f.length){ fileName = f.length > 1 ? f.length + ' files dipilih' : f[0].name; fileSize = Math.round(f[0].size/1024) } else { fileName=null; fileSize=null } }">
                                                </label>
                                                <p class="pl-1">atau tarik ke area ini</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">Format: PDF, DOCX, ZIP, JAVA (Maks. 10MB)</p>
                                        </div>

                                        <!-- Selected File Preview -->
                                        <div class="text-center relative z-10 w-full flex justify-center" x-show="fileName" x-cloak>
                                            <div class="inline-flex items-center p-4 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-indigo-100 dark:border-indigo-900/50 w-full max-w-md transition-all duration-300 transform scale-100">
                                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg mr-4 flex-shrink-0">
                                                    <x-heroicon-s-document-check class="w-6 h-6 text-green-600 dark:text-green-400" />
                                                </div>
                                                <div class="text-left overflow-hidden flex-grow">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate" x-text="fileName"></p>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5"><span x-text="fileSize"></span> KB siap diunggah</p>
                                                </div>
                                                <button type="button" @click.prevent="fileName=null; fileSize=null; $refs.fileInput.value=''" class="ml-3 p-2 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-full text-gray-400 hover:text-red-500 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500" title="Hapus file">
                                                    <x-heroicon-s-trash class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @error('file')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center"><x-heroicon-m-exclamation-circle class="w-4 h-4 mr-1"/>{{ $message }}</p>
                                    @enderror
                                    @if($errors->has('files') || $errors->has('files.*'))
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center"><x-heroicon-m-exclamation-circle class="w-4 h-4 mr-1"/>{!! $errors->first('files') ?? $errors->first('files.*') !!}</p>
                                    @endif
                                    
                                    @if(isset($submission))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center sm:text-left">
                                        <x-heroicon-s-information-circle class="w-4 h-4 inline mr-1 opacity-70"/>
                                        Mengunggah file baru akan menimpa file yang sudah ada sebelumnya.
                                    </p>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 sm:space-x-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ url('/mahasiswa/problem-based-learning') }}" class="w-full sm:w-auto px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-500 transition-all duration-200 text-center">
                                        Batal
                                    </a>
                                    <button type="button" @click="showConfirm=true" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                                        <x-heroicon-s-paper-airplane class="w-5 h-5 mr-2 -rotate-45" />
                                        {{ isset($submission) ? 'Perbarui Tugas' : 'Kirim Tugas Sekarang' }}
                                    </button>
                                </div>

                                <!-- Confirmation Modal -->
                                <div x-show="showConfirm" x-cloak :class="{ 'pointer-events-none': !showConfirm }" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        
                                        <div x-show="showConfirm" 
                                             x-transition:enter="ease-out duration-300" 
                                             x-transition:enter-start="opacity-0" 
                                             x-transition:enter-end="opacity-100" 
                                             x-transition:leave="ease-in duration-200" 
                                             x-transition:leave-start="opacity-100" 
                                             x-transition:leave-end="opacity-0" 
                                             class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showConfirm=false" aria-hidden="true"></div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showConfirm" 
                                             x-transition:enter="ease-out duration-300" 
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                             x-transition:leave="ease-in duration-200" 
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/50 sm:mx-0 sm:h-12 sm:w-12">
                                                        <x-heroicon-o-check-circle class="h-7 w-7 text-blue-600 dark:text-blue-400" />
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                                            Konfirmasi Pengumpulan
                                                        </h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                                Apakah Anda yakin tugas yang Anda unggah sudah final? Pastikan file tidak salah dan jawaban sudah diperiksa kembali.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-4 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-0 sm:space-x-3">
                                                <button type="button" @click="showConfirm=false" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2.5 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm transition-colors duration-200">
                                                    Cek Sekali Lagi
                                                </button>
                                                <button type="submit" @click="showConfirm=false" class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm transition-colors duration-200">
                                                    <x-heroicon-s-paper-airplane class="w-4 h-4 mr-2 -rotate-45"/> Ya, Kumpulkan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Info & Guide -->
                <div class="lg:w-1/3 space-y-6">
                    
                    <!-- Deadline Card -->
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
                    
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 dark:opacity-5">
                            <x-heroicon-s-clock class="w-24 h-24 text-gray-500" />
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center relative z-10">
                            <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg mr-3">
                                <x-heroicon-s-calendar-days class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            Waktu Pengumpulan
                        </h3>
                        
                        <div class="relative z-10">
                            @if($deadline)
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Tenggat Waktu</p>
                                    <p class="text-lg font-bold {{ $lateVisible ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $deadline->isoFormat('D MMMM YYYY') }}
                                    </p>
                                    <p class="text-sm font-medium {{ $lateVisible ? 'text-red-500 dark:text-red-500' : 'text-gray-600 dark:text-gray-300' }} mt-0.5">
                                        Pukul {{ $deadline->format('H:i') }} WIB
                                    </p>
                                    
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center text-sm font-bold {{ $lateVisible ? 'text-red-600 dark:text-red-400' : ($wasSubmittedEarly ? 'text-green-600 dark:text-green-400' : 'text-indigo-600 dark:text-indigo-400') }}">
                                            @if($wasSubmittedLate)
                                                <x-heroicon-s-exclamation-triangle class="w-5 h-5 mr-2"/>
                                                <span>Terlambat {{ $submittedAt->diffForHumans($deadline, ['parts' => 2, 'short' => true]) }}</span>
                                            @elseif($wasSubmittedEarly)
                                                <x-heroicon-s-check-circle class="w-5 h-5 mr-2"/>
                                                <span>Dikirim lebih cepat {{ $deadline->diffForHumans($submittedAt, ['parts' => 2, 'short' => true]) }}</span>
                                            @elseif($noSubmissionPastDeadline)
                                                <x-heroicon-s-exclamation-triangle class="w-5 h-5 mr-2"/> 
                                                <span>Terlambat {{ $deadline->diffForHumans(['parts' => 2, 'short' => true]) }}</span>
                                            @else
                                                <x-heroicon-s-clock class="w-5 h-5 mr-2"/> 
                                                <span>Sisa waktu {{ $deadline->diffForHumans(['parts' => 2, 'short' => true]) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($lateVisible)
                                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-xl text-xs font-medium flex items-start border border-red-200 dark:border-red-800/30">
                                        <x-heroicon-s-information-circle class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" />
                                        <p>Masa pengumpulan telah lewat. Jika Anda mengirim sekarang, status akan menjadi <strong>Terlambat</strong>.</p>
                                    </div>
                                @endif
                            @else
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800/30 flex items-center text-green-700 dark:text-green-300">
                                    <x-heroicon-s-check-badge class="w-10 h-10 mr-4 opacity-80" />
                                    <div>
                                        <p class="font-bold text-base">Tanpa Tenggat Waktu</p>
                                        <p class="text-xs font-medium mt-1">Kerjakan dengan maksimal, tidak perlu terburu-buru.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(isset($submission) && $submission)
                        <!-- Previous Status Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <div class="p-1.5 bg-green-100 dark:bg-green-900/50 rounded-lg mr-3">
                                    <x-heroicon-s-clipboard-document-check class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                Riwayat Tugas
                            </h3>
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Pengumpulan Terakhir</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::parse($submission->submitted_at)->isoFormat('D MMMM YYYY') }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Pukul {{ \Carbon\Carbon::parse($submission->submitted_at)->format('H:i') }} WIB</p>
                                </div>
                                
                                @if($submission->status == 'dinilai')
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800/30">
                                        <p class="text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300 mb-2 flex items-center"><x-heroicon-s-star class="w-4 h-4 mr-1 text-yellow-500"/> Sudah Dinilai</p>
                                        <div class="flex items-end">
                                            <span class="text-3xl font-extrabold text-blue-700 dark:text-blue-400 leading-none">{{ $submission->nilai }}</span>
                                            <span class="text-sm font-bold text-blue-500 dark:text-blue-500 ml-1 mb-1">/100</span>
                                        </div>
                                        @if($submission->feedback)
                                            <div class="mt-4 pt-3 border-t border-blue-200 dark:border-blue-800/50">
                                                <p class="text-xs font-bold text-blue-800 dark:text-blue-300 mb-1">Catatan Dosen:</p>
                                                <p class="text-sm text-blue-900 dark:text-blue-100 italic">"{{ $submission->feedback }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Tips Card -->
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                        <!-- Decorative background -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                        
                        <h3 class="text-lg font-bold mb-4 flex items-center relative z-10">
                            <x-heroicon-s-light-bulb class="w-6 h-6 mr-2 text-yellow-300" />
                            Tips Sukses
                        </h3>
                        <ul class="space-y-4 text-sm font-medium relative z-10">
                            <li class="flex items-start">
                                <div class="bg-white/20 rounded-full p-1 mr-3 mt-0.5">
                                    <x-heroicon-s-check class="w-3 h-3 text-white" />
                                </div>
                                <span>Pastikan kode program bisa di-compile tanpa error.</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-white/20 rounded-full p-1 mr-3 mt-0.5">
                                    <x-heroicon-s-check class="w-3 h-3 text-white" />
                                </div>
                                <span>Beri komentar yang jelas pada logika kode Anda.</span>
                            </li>
                            <li class="flex items-start">
                                <div class="bg-white/20 rounded-full p-1 mr-3 mt-0.5">
                                    <x-heroicon-s-check class="w-3 h-3 text-white" />
                                </div>
                                <span>Periksa kembali nama file sebelum diunggah.</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>document.addEventListener('alpine:init', ()=>{});</script>
<style>[x-cloak]{display:none!important}</style>
