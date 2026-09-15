@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Edit Jawaban - {{ $attempt->user->name ?? 'Unknown' }}</h2>

        <div class="mb-4">
            <div class="text-sm text-gray-600">Pertanyaan:</div>
            <div class="mt-2 p-3 bg-gray-50 border rounded">
                @php
                    $q = $answer->quizQuestion;
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
        </div>

        <form action="{{ route('dosen.kuis.attempts.answers.update', [$attempt->id, $answer->id]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Jawaban Siswa</label>
                @if($q->tipe === 'pilihan_ganda' && is_array($q->opsi_jawaban))
                    <div class="space-y-2">
                        @foreach($q->opsi_jawaban as $idx => $opt)
                            @php
                                $value = $idx;
                                $checked = (string)old('answer', (string)($answer->answer ?? '')) === (string)$value;
                                $isJavaOpt = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$idx]) && $q->opsi_is_java[$idx];
                                $isCorrectOpt = isset($q->jawaban_benar) && ((string)$q->jawaban_benar === (string)$idx);
                            @endphp
                            <label class="flex items-start space-x-3 p-2 border rounded @if($isCorrectOpt) bg-green-50 border-green-300 @endif">
                                <input type="radio" name="answer" value="{{ $value }}" class="mt-1" @if($checked) checked @endif>
                                <div class="flex-1">
                                    @if($isJavaOpt)
                                        <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($opt) }}</code></pre>
                                    @else
                                        {!! (new \Parsedown())->text($opt) !!}
                                    @endif
                                </div>
                            </label>
                        @endforeach
                        @if(isset($q->jawaban_benar))
                            @php
                                $correctKey = $q->jawaban_benar;
                                $isJavaCorrect = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$correctKey]) && $q->opsi_is_java[$correctKey];
                            @endphp
                            <div class="mt-4 p-3 rounded bg-green-50 border border-green-300 text-green-800">
                                <div class="font-semibold">Jawaban Benar:</div>
                                <div class="mt-2">
                                    @if($isJavaCorrect)
                                        <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($q->opsi_jawaban[$correctKey]) }}</code></pre>
                                    @else
                                        {!! (new \Parsedown())->text($q->opsi_jawaban[$correctKey]) !!}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($q->tipe === 'benar_salah')
                    @php $sel = old('answer', $answer->answer ?? ''); @endphp
                    <label class="inline-flex items-center mr-3"><input type="radio" name="answer" value="true" @if($sel === 'true') checked @endif> <span class="ml-2">Benar</span></label>
                    <label class="inline-flex items-center"><input type="radio" name="answer" value="false" @if($sel === 'false') checked @endif> <span class="ml-2">Salah</span></label>
                @else
                    <textarea name="answer_text" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('answer_text', $answer->answer_text ?? $answer->answer) }}</textarea>
                    @error('answer_text') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                @endif
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_correct" value="1" class="form-checkbox" @if(old('is_correct', $answer->is_correct)) checked @endif>
                    <span class="ml-2 text-sm">Tandai sebagai benar</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Nilai (jika diberi manual)</label>
                <input type="number" name="points_awarded" min="0" step="0.01" value="{{ old('points_awarded', $answer->points_awarded) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                @error('points_awarded') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Simpan</button>
                <a href="{{ route('dosen.kuis.attempts.show', $attempt->id) }}" class="text-sm text-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
