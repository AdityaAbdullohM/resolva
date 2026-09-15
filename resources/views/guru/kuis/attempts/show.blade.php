@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Header Card --}}
        @php
            $totalPossible = 0.0;
            foreach ($attempt->answers as $a) {
                $q = $a->quizQuestion;
                $totalPossible += ($q && isset($q->nilai)) ? (float)$q->nilai : 0.0;
            }
            $currentScore = (float) ($attempt->score ?? 0);
            $maxBase = 100.0; // reference base per user's formula
            $finalPercent = $maxBase > 0 ? ($currentScore / $maxBase) * 100 : 0;
            $displayScore = floor($currentScore) == $currentScore
                ? (int) $currentScore
                : rtrim(rtrim(number_format($currentScore, 2, '.', ''), '0'), '.');
            $displayMaxBase = floor((float) $maxBase) == (float) $maxBase
                ? (int) $maxBase
                : rtrim(rtrim(number_format((float) $maxBase, 2, '.', ''), '0'), '.');
            $progressPct = min(100, $finalPercent);
            $statusColor = match($attempt->status) {
                'graded' => 'bg-green-100 text-green-800',
                'completed' => 'bg-blue-100 text-blue-800',
                'started' => 'bg-yellow-100 text-yellow-800',
                default => 'bg-yellow-100 text-yellow-800',
            };
            $statusLabel = match($attempt->status) {
                'graded' => 'Dinilai',
                'completed' => 'Selesai',
                'started' => 'Sedang Berlangsung',
                'pending' => 'Menunggu',
                default => 'Belum Dinilai',
            };
        @endphp

        <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-xl font-bold">{{ strtoupper(substr($attempt->user->name ?? 'U',0,1)) }}</div>
                    <div>
                        <div class="text-lg font-semibold">{{ $attempt->user->name ?? 'Unknown' }}</div>
                        <div class="text-sm opacity-90">Kuis: {{ $attempt->quiz->judul ?? 'Kuis' }}</div>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-sm">Skor</div>
                    <div class="text-2xl font-bold">{{ $displayScore }} / {{ $displayMaxBase }} <span class="text-sm font-medium">({{ rtrim(rtrim(number_format($finalPercent, 2, '.', ''), '0'), '.') }}%)</span></div>
                    <div class="mt-2 flex items-center justify-end space-x-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>
            <div class="h-2 bg-white/20">
                <div class="h-2 bg-white rounded-r" style="width: {{ $progressPct }}%"></div>
            </div>
        </div>

        <form action="{{ route('dosen.kuis.attempts.grade_answers', $attempt->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                @forelse($attempt->answers as $i => $ans)
                    @php
                        $q = $ans->quizQuestion;
                        $studentAnswer = $ans->answer_text ?? $ans->answer ?? null;
                        $isCorrect = $ans->is_correct;
                        $cardColor = $isCorrect ? 'border-green-300 bg-green-50' : ($q && $q->tipe === 'isian' ? 'border-yellow-300 bg-yellow-50' : 'border-red-300 bg-red-50');
                    @endphp

                    <div class="flex items-start space-x-4 p-4 rounded-lg border-l-4 {{ $cardColor }} shadow-sm">
                        <div class="w-8 text-center font-bold text-gray-700">{{ $i + 1 }}</div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 mb-1">
                                    @php
                                    $text = $q->pertanyaan ?? '';
                                    $javaHtml = !empty($q->pertanyaan_java) ? '<pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">' . e($q->pertanyaan_java) . '</code></pre>' : '';
                                    $mainImageHtml = !empty($q->gambar) ? '<div class="mt-2"><img src="' . e(asset('storage/' . $q->gambar)) . '" alt="Gambar Soal" class="max-w-full rounded shadow-sm"></div>' : '';

                                    if (\Illuminate\Support\Str::contains($text, '<img')) {
                                        echo $text;
                                    } else {
                                        $parts = preg_split('/(\[gambar\]|\[java\])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
                                        $out = '';
                                        $pd = new \Parsedown();
                                        foreach ($parts as $part) {
                                            if ($part === '[gambar]') {
                                                $out .= $mainImageHtml;
                                            } elseif ($part === '[java]') {
                                                $out .= $javaHtml;
                                            } else {
                                                $out .= $pd->text($part);
                                            }
                                        }
                                        echo $out;
                                    }
                                @endphp

                                    @if(empty($q->pertanyaan) || !\Illuminate\Support\Str::contains($q->pertanyaan ?? '', '[java]'))
                                    @if(!empty($q->pertanyaan_java) && !\Illuminate\Support\Str::contains($q->pertanyaan ?? '', '[java]'))
                                        <div class="mt-2">
                                            <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($q->pertanyaan_java) }}</code></pre>
                                        </div>
                                    @endif
                                @endif

                                @if(empty($q->pertanyaan) || !\Illuminate\Support\Str::contains($q->pertanyaan ?? '', '[gambar]'))
                                    @if(!empty($q->gambar))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $q->gambar) }}" alt="Gambar Soal" class="max-w-full rounded shadow-sm">
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if($q->tipe === 'pilihan_ganda' && is_array($q->opsi_jawaban))
                                <div class="mb-2">
                                    @foreach($q->opsi_jawaban as $idx => $opt)
                                        @php
                                            $isJavaOpt = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$idx]) && $q->opsi_is_java[$idx];
                                        @endphp
                                        <div class="flex items-start space-x-2 text-sm">
                                            <div class="w-5 pt-1">@if($q->jawaban_benar == $idx) <span class="text-green-600">●</span> @endif</div>
                                            <div class="text-gray-700 flex-1">
                                                @if($isJavaOpt)
                                                      <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($opt) }}</code></pre>
                                                @else
                                                    {!! (new \Parsedown())->text($opt) !!}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="text-sm mb-2">
                                <div class="text-gray-600">Jawaban siswa:</div>
                                <div class="mt-1 p-3 bg-white rounded border">
                                    @if ($q->tipe === 'pilihan_ganda')
                                        @if(isset($q->opsi_jawaban[$ans->answer]))
                                            @php
                                                $optKey = $ans->answer;
                                                $isJavaOption = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$optKey]) && $q->opsi_is_java[$optKey];
                                            @endphp
                                            @if($isJavaOption)
                                                <div class="mt-2">
                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($q->opsi_jawaban[$optKey]) }}</code></pre>
                                                </div>
                                            @else
                                                {!! (new \Parsedown())->text($q->opsi_jawaban[$optKey]) !!}
                                            @endif
                                        @else
                                            <span class="italic text-gray-500">Tidak dijawab</span>
                                        @endif
                                    @elseif ($q->tipe === 'benar_salah')
                                        {{ $ans->answer == 'true' ? 'Benar' : 'Salah' }}
                                    @else
                                        {{ $ans->answer_text ?? $ans->answer ?? 'Tidak dijawab' }}
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium">Benar:</span>
                                    <span class="ml-2">{{ $isCorrect ? 'Ya' : 'Tidak' }}</span>
                                </div>

                                <div class="text-right">
                                    @if($q->tipe === 'isian')
                                        <label class="block text-xs font-medium text-gray-600">Nilai (input):</label>
                                        <div class="flex items-center space-x-3 mt-1">
                                            <input type="number" name="points[{{ $ans->id }}]" min="0" step="0.01" value="{{ old('points.' . $ans->id, $ans->points_awarded) }}" class="shadow-sm border rounded px-2 py-1 w-28">
                                            @if($ans->points_awarded !== null)
                                                @php
                                                    $p = (float) $ans->points_awarded;
                                                    $pDisplay = floor($p) == $p
                                                        ? (int) $p
                                                        : rtrim(rtrim(number_format($p, 2, '.', ''), '0'), '.');
                                                @endphp
                                                <div class="text-xs text-gray-700">Tersimpan: <span class="font-semibold">{{ $pDisplay }}</span></div>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $displayPoints = null;
                                            if ($ans->points_awarded !== null) {
                                                $displayPoints = $ans->points_awarded;
                                            } elseif ($q->tipe === 'pilihan_ganda') {
                                                $displayPoints = $ans->is_correct ? ($q->nilai ?? 0) : 0;
                                            }
                                        @endphp
                                        @if($displayPoints !== null)
                                            @php
                                                $pointsDisplay = floor((float) $displayPoints) == (float) $displayPoints
                                                    ? (int) $displayPoints
                                                    : rtrim(rtrim(number_format((float) $displayPoints, 2, '.', ''), '0'), '.');
                                            @endphp
                                            <div class="text-sm">
                                                <span class="font-medium">Nilai:</span>
                                                <span class="ml-2 font-semibold text-gray-800">{{ $pointsDisplay }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if (!$isCorrect)
                                <div class="mt-3">
                                    <span class="font-semibold text-sm text-green-700">Jawaban Benar:</span>
                                    <div class="mt-1 p-3 rounded-md bg-green-100 border border-green-300 text-gray-800">
                                        @if ($q->tipe === 'pilihan_ganda')
                                            @php
                                                $correctKey = $q->jawaban_benar;
                                                $isJavaCorrect = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$correctKey]) && $q->opsi_is_java[$correctKey];
                                            @endphp
                                            @if($isJavaCorrect)
                                                <div class="mt-2">
                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($q->opsi_jawaban[$correctKey]) }}</code></pre>
                                                </div>
                                            @else
                                                {!! (new \Parsedown())->text($q->opsi_jawaban[$correctKey]) !!}
                                            @endif
                                        @elseif ($q->tipe === 'benar_salah')
                                            {{ $q->jawaban_benar == 'true' ? 'Benar' : 'Salah' }}
                                        @else
                                            {{ $q->jawaban_benar }}
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if(!empty($q->explanation))
                                <div class="mt-3 text-sm text-gray-600">
                                    <strong>Penjelasan:</strong>
                                    <div class="mt-1">{!! nl2br(e($q->explanation)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                @empty
                    <div class="p-4 text-sm text-gray-600">Belum ada jawaban untuk attempt ini.</div>
                @endforelse
            </div>

            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-600">Mulai: <span class="font-medium">{{ $attempt->start_time ? $attempt->start_time->format('d M Y, H:i') : '-' }}</span> &middot; Selesai: <span class="font-medium">{{ $attempt->end_time ? $attempt->end_time->format('d M Y, H:i') : '-' }}</span></div>
                <div class="flex items-center space-x-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">Simpan Nilai Isian</button>
                    <a href="{{ route('dosen.kuis.rekap', $attempt->quiz->id) }}" class="text-sm text-gray-600">Kembali ke Rekap</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
