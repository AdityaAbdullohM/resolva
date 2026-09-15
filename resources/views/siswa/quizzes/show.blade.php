@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen overflow-x-hidden quiz-page">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white dark:bg-slate-900 dark:text-gray-100 overflow-hidden shadow-xl sm:rounded-2xl">
            {{-- Header Section --}}
            <div class="bg-indigo-600 dark:bg-indigo-700 p-6 sm:p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 dark:bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-indigo-500/30 dark:bg-indigo-500/30 rounded-full blur-2xl"></div>

                <div class="relative z-10 w-full">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6 min-w-0 w-full">
                        <div class="flex-1 min-w-0 w-full">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 dark:bg-white/10 text-white border border-white/20 backdrop-blur-sm">
                                    {{ $quiz->mataPelajaran->nama ?? 'Umum' }}
                                </span>
                                @if($attempt && ($attempt->status ?? '') === 'finished')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/80 text-white border border-green-400/50 backdrop-blur-sm">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                            
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-4">{{ $quiz->title }}</h1>
                            <div class="text-indigo-100 text-lg leading-relaxed max-w-full opacity-90 break-words [&>p]:mb-4 [&>p:last-child]:mb-0">
                                @markdown($quiz->description)
                            </div>
                        </div>

                        @if (($attempt === null || ($attempt->status ?? '') !== 'finished') && $quiz->duration > 0)
                            <div class="w-full sm:w-auto sm:flex-shrink-0 bg-white/10 dark:bg-slate-700/40 backdrop-blur-md border border-white/20 dark:border-slate-600 rounded-xl p-4 sm:min-w-[160px] text-center shadow-lg transform transition-all hover:scale-105">
                                <p class="text-xs text-indigo-200 uppercase tracking-wider font-bold mb-1">Sisa Waktu</p>
                                <div id="timer" class="text-2xl sm:text-3xl font-mono font-bold text-white tracking-tight">
                                    --:--
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 text-sm font-medium text-indigo-100 border-t border-indigo-500/30 pt-6">
                        <div class="flex items-center bg-indigo-700/30 px-3 py-1.5 rounded-lg w-full">
                            <svg class="w-5 h-5 mr-2 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $quiz->questions->count() }} Pertanyaan
                        </div>
                        <div class="flex items-center bg-indigo-700/30 px-3 py-1.5 rounded-lg w-full">
                            <svg class="w-5 h-5 mr-2 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $quiz->duration ? $quiz->duration . ' Menit' : 'Tidak terbatas' }}
                        </div>
                        @if($quiz->end_time)
                            <div class="flex items-center bg-indigo-700/30 px-3 py-1.5 rounded-lg w-full">
                                <svg class="w-5 h-5 mr-2 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Batas: {{ \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Body Section --}}
            <div class="p-6 sm:p-10 bg-white dark:bg-slate-900 dark:text-gray-200">
                @if (session('success'))
                    <div class="mb-8 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 dark:border-green-700 p-4 rounded-r-lg shadow-sm flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Berhasil!</h3>
                            <div class="mt-1 text-sm text-green-700 dark:text-green-200">
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-700 p-4 rounded-r-lg shadow-sm flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Terjadi Kesalahan!</h3>
                            <div class="mt-1 text-sm text-red-700 dark:text-red-200">
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 quiz-content">
                    @if ($attempt && ($attempt->status ?? '') === 'finished')
                        @include('siswa.quizzes._show_quiz_with_attempt', ['quiz' => $quiz, 'attempt' => $attempt])
                    @else
                        @include('siswa.quizzes._show_quiz_without_attempt', ['quiz' => $quiz, 'attempt' => $attempt ?? null])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.quiz-page { overflow-x:hidden; }
.quiz-page .quiz-content { min-width:0; max-width:100%; }
.quiz-page .quiz-content img { max-width:100% !important; height:auto !important; }
.quiz-page .quiz-content pre, .quiz-page .quiz-content code { white-space: pre-wrap !important; word-break: break-word !important; }
.quiz-page .quiz-content .flex-1, .quiz-page .quiz-content .flex { min-width:0 !important; }
.quiz-page .quiz-content .question-block, .quiz-page .quiz-content .quiz-card { min-width:0 !important; }
.quiz-page .quiz-content .w-full { max-width:100% !important; }
@media (max-width: 640px) {
    .quiz-page .text-5xl { font-size: 2.5rem; }
    .quiz-page .p-10 { padding: 1.5rem; }
}
</style>
@endpush

@push('scripts')
@if (($attempt === null || ($attempt->status ?? '') !== 'finished') && $quiz->duration > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timerElement = document.getElementById('timer');
        const quizForm = document.getElementById('quiz-form');
        const quizId = {{ $quiz->id }};
        const duration = {{ $quiz->duration }} * 60; // in seconds

        let deadline = localStorage.getItem('quiz_deadline_' + quizId);

        if (!deadline) {
            deadline = new Date().getTime() + duration * 1000;
            localStorage.setItem('quiz_deadline_' + quizId, deadline);
        }

        function updateTimer() {
            const now = new Date().getTime();
            const distance = deadline - now;

            if (distance < 0) {
                clearInterval(interval);
                timerElement.innerHTML = "00:00:00";
                timerElement.classList.add('text-red-200');
                localStorage.removeItem('quiz_deadline_' + quizId);
                quizForm.submit();
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Format with leading zeros
            const h = hours < 10 ? "0" + hours : hours;
            const m = minutes < 10 ? "0" + minutes : minutes;
            const s = seconds < 10 ? "0" + seconds : seconds;

            if (hours > 0) {
                timerElement.innerHTML = `${h}:${m}:${s}`;
            } else {
                timerElement.innerHTML = `${m}:${s}`;
            }
            
            // Visual warning when time is low (less than 1 minute)
            if (distance < 60000) {
                timerElement.classList.remove('text-white');
                timerElement.classList.add('text-red-300', 'animate-pulse');
            }
        }

        const interval = setInterval(updateTimer, 1000);
        updateTimer(); // Run immediately

        quizForm.addEventListener('submit', function() {
            localStorage.removeItem('quiz_deadline_' + quizId);
        });
    });
</script>
@endif
<!-- Prism for Java highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>

@endpush
