<x-app-layout>
    <div class="py-4 sm:py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl">
                {{-- Header Section --}}
                <div class="p-4 sm:p-10 relative overflow-hidden rounded-t-2xl" style="background: linear-gradient(90deg,#4f46e5 0%,#6366f1 50%,#7c3aed 100%);">
                    <div class="relative z-10 max-w-5xl mx-auto text-white">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight break-words">{{ $materi->judul }}</h3>
                                <p class="mt-2 text-sm sm:text-base opacity-90 max-w-2xl leading-relaxed">{!! \Illuminate\Support\Str::limit(strip_tags($materi->deskripsi), 180) !!}</p>

                                <div class="mt-4 flex flex-wrap gap-2 sm:gap-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium bg-white/20 backdrop-blur-sm border border-white/30">{{ $materi->mataPelajaran->nama }}</span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium bg-white/20 backdrop-blur-sm border border-white/30">Kelas {{ $materi->kelas->nama }}</span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-medium bg-white/20">{{ $materi->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                            
                            </div>
                        </div>
                    </div>
                    <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at 10% 10%, rgba(255,255,255,0.08), transparent 10%), radial-gradient(circle at 90% 90%, rgba(0,0,0,0.06), transparent 20%);"></div>
                </div>

                <div id="materi-content" class="p-4 sm:p-10 text-gray-900 dark:text-gray-100 space-y-6 sm:space-y-8 bg-white dark:bg-gray-800">
                    <div class="prose prose-sm sm:prose-base max-w-none text-gray-700 dark:text-gray-300 leading-relaxed dark:prose-invert">
                        {!! $materi->deskripsi !!}
                    </div>

                    @if ($materi->link_url)
                        <div class="bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500 dark:border-blue-400 p-3 sm:p-4 rounded-r-md shadow-sm text-blue-800 dark:text-blue-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Tautan Eksternal</h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        <a href="{{ $materi->link_url }}" target="_blank" class="hover:underline font-medium">{{ $materi->link_url }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($materi->materiFiles->count() > 0)
                        <div>
                            <h4 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3 sm:mb-4 flex items-center border-b pb-2">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lampiran Materi
                            </h4>
                            <div class="space-y-4 sm:space-y-6">
                                @foreach ($materi->materiFiles as $file)
                                    @php
                                        $extension = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isPdf = $extension == 'pdf';
                                    @endphp
                                    
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 bg-white dark:bg-gray-800">
                                        <div class="px-3 sm:px-5 py-3 border-b border-gray-200 dark:border-b-gray-600 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex items-center space-x-3 overflow-hidden min-w-0">
                                                @if($isPdf)
                                                    <span class="text-red-500 flex-shrink-0"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg></span>
                                                @elseif($isImage)
                                                    <span class="text-purple-500 flex-shrink-0"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg></span>
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-300 flex-shrink-0"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg></span>
                                                @endif
                                                <span class="font-medium text-gray-700 dark:text-gray-100 truncate text-sm sm:text-base" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 flex-shrink-0">
                                                <a href="{{ route('mahasiswa.materi.downloadFile', ['kelas' => $materi->kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium flex items-center transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    Unduh
                                                </a>
                                                @if($isPdf)
                                                    <a href="{{ route('mahasiswa.materi.viewFile', ['kelas' => $materi->kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" target="_blank" class="text-sm text-green-600 dark:text-green-300 hover:text-green-800 font-medium flex items-center transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                        Buka
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-4">
                                            @if ($isImage)
                                                <div class="flex justify-center bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4 border border-dashed border-gray-200 dark:border-gray-600">
                                                    <img src="{{ route('mahasiswa.materi.viewFile', ['kelas' => $materi->kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" alt="{{ $file->original_name }}" class="max-h-[280px] sm:max-h-[500px] rounded shadow-sm object-contain">
                                                </div>
                                            @elseif ($isPdf)
                                                <div class="aspect-w-16 aspect-h-9 h-[320px] sm:h-[600px] rounded-lg overflow-hidden border border-gray-200 shadow-inner">
                                                    <iframe src="{{ route('mahasiswa.materi.viewFile', ['kelas' => $materi->kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" class="w-full h-full" style="border: none;"></iframe>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center justify-center h-24 sm:h-32 text-gray-400 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                                    <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span class="text-sm">Pratinjau tidak tersedia untuk jenis file ini.</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 sm:pt-6 border-t border-gray-100 flex justify-center sm:justify-end">
                        <a href="{{ route('mahasiswa.materis.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-4 sm:px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Daftar Materi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>