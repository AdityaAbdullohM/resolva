@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <div class="rounded-lg overflow-hidden shadow-md bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold">Rekap Hasil</h1>
                        <div class="text-sm opacity-90 mt-1">Kuis: <span class="font-semibold">{{ $kuis->title }}</span></div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('dosen.kuis.rekap.export', $kuis->id) }}" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded shadow">Download Excel (.xlsx)</a>
                        <a href="{{ route('dosen.kuis.index') }}" class="inline-flex items-center bg-white/10 hover:bg-white/20 text-white text-sm font-medium py-2 px-4 rounded">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $totalPossible = 0.0;
            if(isset($kuis->questions) && is_iterable($kuis->questions)){
                foreach($kuis->questions as $qq){
                    $totalPossible += ($qq && isset($qq->nilai)) ? (float)$qq->nilai : 0.0;
                }
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Peserta</div>
                <div class="text-2xl font-bold text-gray-800 mt-2">{{ $attempts->count() }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Nilai Per Kuis</div>
                <div class="text-2xl font-bold text-indigo-600 mt-2">{{ (int) round($totalPossible) }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Rata-rata Skor</div>
                <div class="text-2xl font-bold text-emerald-600 mt-2">
                    @php
                        $avg = $attempts->count() ? round($attempts->avg('score'), 2) : 0;
                    @endphp
                    {{ (int) round($avg) }}%
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="text-left text-sm text-white uppercase bg-indigo-600">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Skor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $i => $attempt)
                        @php
                            $current = $attempt->score ?? 0;
                            $maxBase = 100.0; // max score per requirement
                            $finalPercent = $maxBase > 0 ? round(($current / $maxBase) * 100) : 0;
                            $pct = min(100, $finalPercent);
                            $status = $attempt->status;
                            // If the attempt has an end_time (i.e. student submitted), show 'Sudah Dinilai'
                            if (!is_null($attempt->end_time)) {
                                $statusLabel = 'Sudah Dinilai';
                                $statusClass = 'bg-green-100 text-green-800';
                            } else {
                                $statusLabel = match($status) {
                                    'graded' => 'Dinilai',
                                    'completed' => 'Selesai',
                                    'started' => 'Sedang Berlangsung',
                                    'pending' => 'Menunggu',
                                    default => 'Belum Dinilai',
                                };
                                $statusClass = match($status) {
                                    'graded' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'started' => 'bg-yellow-100 text-yellow-800',
                                    'pending' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            }
                        @endphp

                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-6 py-4 align-top">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 align-top">
                                <div class="font-medium text-gray-800">{{ $attempt->user->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $attempt->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top w-48">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-semibold text-gray-800">{{ (int) round($current) }} / {{ (int) round($maxBase) }}</div>
                                        <div class="text-xs text-gray-100 bg-gray-800/10 rounded px-2 py-0.5">{{ (int) round($finalPercent) }}%</div>
                                </div>
                                <div class="mt-2 w-full bg-gray-100 rounded h-2">
                                    <div class="h-2 rounded bg-indigo-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-gray-600">
                                <div class="text-sm">
                                    @php
                                        $displayStart = null;
                                        if ($attempt->start_time) {
                                            $displayStart = $attempt->start_time;
                                        }
                                        // Only estimate start time when start_time is missing.
                                        // Do not estimate when start_time equals end_time — show stored values instead.
                                        if (is_null($attempt->start_time) && $attempt->end_time && isset($kuis->duration) && $kuis->duration > 0) {
                                            try {
                                                $displayStart = $attempt->end_time->copy()->subMinutes((int)$kuis->duration);
                                            } catch (\Exception $e) {
                                                // fallback to original start_time
                                                $displayStart = $attempt->start_time;
                                            }
                                        }
                                    @endphp

                                    <div>
                                        <span class="font-medium">Mulai:</span>
                                        {{ $displayStart ? $displayStart->format('d M Y, H:i:s') : '-' }}
                                        @if($displayStart && $attempt->start_time && !$displayStart->equalTo($attempt->start_time))
                                            <small class="text-xs text-gray-400">(estimasi)</small>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium">Selesai:</span>
                                        {{ $attempt->end_time ? $attempt->end_time->format('d M Y, H:i:s') : '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('dosen.kuis.attempts.show', $attempt->id) }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-1 px-3 rounded">Detail</a>
                                    <form action="{{ route('dosen.kuis.attempts.destroy', $attempt->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus attempt ini?')" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1 px-3 rounded">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada peserta yang mengerjakan kuis ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
