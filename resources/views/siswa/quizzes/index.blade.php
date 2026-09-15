@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-indigo-600 to-teal-400">Daftar Kuis</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 rounded-md shadow-md" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="h-6 w-6 text-green-500 dark:text-green-400 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($quizzes->isEmpty())
        <div class="bg-blue-100 dark:bg-blue-900/30 border-l-4 border-blue-500 text-blue-700 dark:text-blue-400 p-6 rounded-md shadow-lg text-center">
            <svg class="mx-auto h-12 w-12 text-blue-500 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            <h2 class="text-2xl font-bold mt-4 text-gray-900 dark:text-white">Belum Ada Kuis</h2>
            <p class="mt-2 text-lg text-gray-700 dark:text-gray-300">Saat ini belum ada kuis yang tersedia untuk Anda. Silakan periksa kembali nanti.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach ($quizzes as $quiz)
                @php
                    $attempt = $quiz->attempts->first();
                    $isCompleted = $attempt !== null;
                    $isExpired = $quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->isPast() : false;
                @endphp
                <div class="pbl-card-accent bg-white/95 dark:bg-gray-900/60 rounded-xl overflow-hidden transition-transform transform hover:-translate-y-2 duration-300 ease-in-out {{ $isCompleted ? 'ring-2 ring-green-400 dark:ring-green-600' : 'border border-gray-200 dark:border-gray-700' }}">
                    <div class="p-6">
                        <div class="mb-4">
                            <span class="inline-block text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-indigo-200 to-pink-200 text-indigo-900 dark:from-indigo-700 dark:to-pink-700 dark:text-indigo-100">{{ $quiz->mataPelajaran->nama ?? 'Umum' }}</span>
                            @if($isCompleted)
                                <span class="inline-block bg-green-50 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Selesai</span>
                            @endif
                        </div>

                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2 truncate">{{ $quiz->title }}</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 h-16 overflow-hidden">{{ Str::limit($quiz->description, 120) }}</p>

                        <div class="text-sm text-gray-500 dark:text-gray-400 space-y-2 mb-5">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <span>Dosen: {{ $quiz->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $quiz->questions->count() }} Pertanyaan</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                <span>Durasi: {{ $quiz->duration ? $quiz->duration . ' menit' : 'Tidak terbatas' }}</span>
                            </div>
                            @if($quiz->end_time)
                                <div class="flex items-center {{ \Carbon\Carbon::parse($quiz->end_time)->isPast() ? 'text-red-500 dark:text-red-400' : '' }}">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">Batas Waktu: {{ \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($isCompleted)
                                <div class="text-center">
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Skor Anda:</p>
                                <p class="text-4xl font-bold text-green-600 dark:text-green-400">{{ round($attempt->score) }}<span class="text-2xl">%</span></p>
                                <a href="{{ route('mahasiswa.quizzes.show', $quiz) }}" class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow-md transition-colors duration-300">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                      </svg>
                                    Lihat Hasil
                                </a>
                            </div>
                        @else
                            @if($isExpired)
                                <button disabled class="mt-auto inline-flex items-center justify-center w-full px-4 py-3 bg-gray-400 dark:bg-gray-700 text-white font-bold rounded-lg shadow-inner cursor-not-allowed" title="Batas waktu kuis telah lewat">
                                    <svg class="h-6 w-6 mr-2 opacity-80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                    Kuis Berakhir
                                </button>
                            @else
                                <a href="{{ route('mahasiswa.quizzes.show', $quiz) }}" class="mt-auto inline-flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-xl transition-transform transform hover:scale-105 duration-300">
                                    <svg class="h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                    Mulai Kuis
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection