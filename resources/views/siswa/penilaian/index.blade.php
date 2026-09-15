@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Daftar Nilai</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pantau hasil pengerjaan tugas dan perkembangan belajar Anda.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tugas & Mata Kuliah
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Skor
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Feedback
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($submissions as $submission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $submission->problem->judul }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $submission->problem->mataPelajaran->nama ?? 'Umum' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($submission->status) {
                                            case 'submitted':
                                                $statusClass = 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50';
                                                $statusText = 'Menunggu Penilaian';
                                                break;
                                            case 'graded':
                                            case 'dinilai':
                                                $statusClass = 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800/50';
                                                $statusText = 'Selesai Dinilai';
                                                break;
                                            case 'late':
                                                $statusClass = 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800/50';
                                                $statusText = 'Terlambat';
                                                break;
                                            default:
                                                $statusClass = 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600';
                                                $statusText = ucfirst($submission->status ?? 'Belum dikirim');
                                                break;
                                        }
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($submission->score || $submission->nilai)
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold {{ ($submission->score ?? $submission->nilai) >= 75 ? 'text-green-600 dark:text-green-400' : 'text-orange-500 dark:text-orange-400' }}">
                                                {{ $submission->score ?? $submission->nilai }}
                                            </span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">/100</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($submission->feedback)
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2" title="{{ $submission->feedback }}">{{ $submission->feedback }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada feedback</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('mahasiswa.problems.show', $submission->problem) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold flex items-center justify-end group">
                                        Detail
                                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-full p-4 mb-4">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Belum ada penilaian</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tugas yang Anda kerjakan dan dinilai akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection