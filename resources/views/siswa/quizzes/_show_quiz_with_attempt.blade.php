            <div class="p-6">
                <div class="bg-blue-50 dark:bg-slate-800 dark:bg-opacity-20 p-4 sm:p-6 rounded-xl mb-6 sm:mb-8 shadow-sm overflow-hidden">
                    <h2 class="text-2xl font-extrabold text-blue-800 dark:text-blue-200 text-center mb-2">Hasil Kuis Anda</h2>
                    {{-- Start and end times hidden for student view --}}
                    @php
                        $totalScore = $attempt->answers->reduce(function($carry, $ans) {
                            $q = $ans->question;
                            $val = 0;
                            if ($q && isset($q->nilai)) {
                                $val = (float) $q->nilai;
                            }
                            return $carry + ($ans->is_correct ? $val : 0);
                        }, 0);

                        $displayTotal = (int) round($totalScore);
                    @endphp
                    <p class="text-center text-4xl sm:text-5xl font-extrabold text-blue-600 dark:text-blue-300 mb-6">
                        {{ $displayTotal }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 text-center">
                        <div class="bg-green-100 dark:bg-green-900 dark:bg-opacity-20 p-5 rounded-lg shadow-sm">
                            <p class="text-xl font-bold text-green-800 dark:text-green-200">Benar</p>
                            <p class="text-3xl font-extrabold text-green-700 dark:text-green-300">{{ $attempt->answers->where('is_correct', true)->count() }}</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900 dark:bg-opacity-20 p-5 rounded-lg shadow-sm">
                            <p class="text-xl font-bold text-red-800 dark:text-red-200">Salah</p>
                            <p class="text-3xl font-extrabold text-red-700 dark:text-red-300">{{ $attempt->answers->where('is_correct', false)->count() }}</p>
                        </div>
                        <div class="bg-gray-100 dark:bg-slate-700 p-5 rounded-lg shadow-sm">
                            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">Total Soal</p>
                            <p class="text-3xl font-extrabold text-gray-700 dark:text-gray-300">{{ $quiz->questions->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8 mt-8 w-full max-w-full">
                    @foreach ($attempt->answers as $index => $answer)
                        @php
                                $typeClass = $answer->question->tipe === 'pilihan_ganda' ? 'border-l-4 border-blue-400' : ($answer->question->tipe === 'isian' ? 'border-l-4 border-yellow-400' : ($answer->question->tipe === 'benar_salah' ? 'border-l-4 border-indigo-400' : 'border-l-4 border-gray-300'));
                                $letters = ['A','B','C','D','E'];
                            @endphp
                        <div class="bg-white dark:bg-slate-800 dark:text-gray-200 p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden {{ $typeClass }}">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 min-w-0 w-full">
                                <div class="text-base sm:text-lg font-bold text-gray-800 dark:text-gray-100 mb-2 sm:mb-0 flex-1 flex flex-col sm:flex-row sm:items-start min-w-0">
                                    <span class="mr-2 mb-2 sm:mb-0">{{ $index + 1 }}.</span>
                                    <div class="flex-1 min-w-0 w-full font-normal [&>p]:mb-4 [&>p:last-child]:mb-0 [&>pre]:bg-gray-800 dark:[&>pre]:bg-slate-900 [&>pre]:text-gray-100 [&>pre]:p-4 [&>pre]:rounded-md [&>pre]:overflow-x-auto [&>pre]:my-4 [&>code]:bg-gray-200 dark:[&>code]:bg-slate-700 [&>code]:text-gray-800 dark:[&>code]:text-gray-200 [&>code]:px-1.5 [&>code]:py-0.5 [&>code]:rounded break-words">
                                        @php
                                            $q = $answer->question;
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
                                                        <img src="{{ asset('storage/' . $q->gambar) }}" alt="Gambar Soal" class="w-full rounded shadow-sm">
                                                    </div>
                                                @endif
                                            @endif
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-white px-3 py-1 rounded-full whitespace-nowrap ml-0 sm:ml-4
                                    @switch($answer->question->tipe)
                                        @case('pilihan_ganda') bg-blue-600 @break
                                        @case('benar_salah') bg-purple-600 @break
                                        @case('isian') bg-yellow-600 @break
                                    @endswitch">
                                    {{ str_replace('_', ' ', Str::title($answer->question->tipe)) }}
                                </span>
                            </div>

                            <div class="mt-4 space-y-3 sm:pl-6 sm:border-l-2 border-l-0 pl-0 border-gray-200/80 dark:border-gray-600/80 sm:ml-2">
                                <div>
                                    <span class="font-semibold text-sm @if($answer->is_correct) text-green-700 dark:text-green-300 @else text-red-700 dark:text-red-300 @endif">Jawaban Anda:</span>
                                    <div class="mt-1 p-3 rounded-md @if($answer->is_correct) bg-green-100 dark:bg-green-800/30 @else bg-red-100 dark:bg-red-800/30 @endif border @if($answer->is_correct) border-green-300 dark:border-green-700 @else border-red-300 dark:border-red-700 @endif text-gray-800 dark:text-gray-200">
                                        @if ($answer->question->tipe === 'pilihan_ganda')
                                            @if(isset($answer->question->opsi_jawaban[$answer->answer]))
                                                <div class="[&>p]:mb-2 [&>p:last-child]:mb-0 [&>pre]:bg-gray-800 dark:[&>pre]:bg-slate-900 [&>pre]:text-gray-100 [&>pre]:p-3 [&>pre]:rounded-md [&>pre]:overflow-x-auto [&>pre]:mt-2 [&>code]:bg-gray-200 dark:[&>code]:bg-slate-700 [&>code]:text-gray-800 dark:[&>code]:text-gray-200 [&>code]:px-1.5 [&>code]:py-0.5 [&>code]:rounded">
                                                    @php
                                                        $optKey = $answer->answer;
                                                        $isJavaOption = is_array($answer->question->opsi_is_java) && isset($answer->question->opsi_is_java[$optKey]) && $answer->question->opsi_is_java[$optKey];
                                                        $optLetter = $letters[$optKey] ?? strtoupper(chr(65 + $optKey));
                                                    @endphp
                                                    <div class="flex items-start">
                                                        <div class="flex-shrink-0 mr-4">
                                                            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-700 text-indigo-700 dark:text-white font-semibold flex items-center justify-center text-sm">{{ $optLetter }}</div>
                                                        </div>
                                                        <div class="flex-1">
                                                            @if($isJavaOption)
                                                                <div class="mt-2">
                                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($answer->question->opsi_jawaban[$optKey]) }}</code></pre>
                                                                </div>
                                                            @else
                                                                {!! (new \Parsedown())->text($answer->question->opsi_jawaban[$optKey]) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="italic text-gray-500">Tidak dijawab</span>
                                            @endif
                                        @elseif ($answer->question->tipe === 'benar_salah')
                                            {{ $answer->answer == 'true' ? 'Benar' : 'Salah' }}
                                        @else
                                            {{ $answer->answer ?? 'Tidak dijawab' }}
                                        @endif
                                    </div>
                                </div>

                                @if (!$answer->is_correct)
                                    <div class="mt-3">
                                        <span class="font-semibold text-sm text-green-700 dark:text-green-300">Jawaban Benar:</span>
                                        <div class="mt-1 p-3 rounded-md bg-green-100 dark:bg-green-800/30 border border-green-300 dark:border-green-700 text-gray-800 dark:text-gray-200">
                                            @if ($answer->question->tipe === 'pilihan_ganda')
                                                <div class="[&>p]:mb-2 [&>p:last-child]:mb-0 [&>pre]:bg-gray-800 dark:[&>pre]:bg-slate-900 [&>pre]:text-gray-100 [&>pre]:p-3 [&>pre]:rounded-md [&>pre]:overflow-x-auto [&>pre]:mt-2 [&>code]:bg-gray-200 dark:[&>code]:bg-slate-700 [&>code]:text-gray-800 dark:[&>code]:text-gray-200 [&>code]:px-1.5 [&>code]:py-0.5 [&>code]:rounded">
                                                    @php
                                                        $correctKey = $answer->question->jawaban_benar;
                                                        $isJavaCorrect = is_array($answer->question->opsi_is_java) && isset($answer->question->opsi_is_java[$correctKey]) && $answer->question->opsi_is_java[$correctKey];
                                                        $correctLetter = $letters[$correctKey] ?? strtoupper(chr(65 + $correctKey));
                                                    @endphp
                                                    <div class="flex items-start">
                                                        <div class="flex-shrink-0 mr-4">
                                                            <div class="w-7 h-7 rounded-full bg-green-100 text-green-700 font-semibold flex items-center justify-center text-sm">{{ $correctLetter }}</div>
                                                        </div>
                                                        <div class="flex-1">
                                                            @if($isJavaCorrect)
                                                                <div class="mt-2">
                                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($answer->question->opsi_jawaban[$correctKey]) }}</code></pre>
                                                                </div>
                                                            @else
                                                                {!! (new \Parsedown())->text($answer->question->opsi_jawaban[$correctKey]) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($answer->question->tipe === 'benar_salah')
                                                 {{ $answer->question->jawaban_benar == 'true' ? 'Benar' : 'Salah' }}
                                            @else
                                                {{ $answer->question->jawaban_benar }}
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($answer->question->explanation)
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-md">
                                        <p class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">Penjelasan:</p>
                                        <div class="text-sm text-gray-700 dark:text-gray-200 [&>p]:mb-2 [&>p:last-child]:mb-0 [&>pre]:bg-gray-800 dark:[&>pre]:bg-slate-900 [&>pre]:text-gray-100 [&>pre]:p-3 [&>pre]:rounded-md [&>pre]:overflow-x-auto [&>pre]:mt-2 [&>code]:bg-gray-200 dark:[&>code]:bg-slate-700 [&>code]:text-gray-800 dark:[&>code]:text-gray-200 [&>code]:px-1.5 [&>code]:py-0.5 [&>code]:rounded">
                                            {!! (new \Parsedown())->text($answer->question->explanation) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="{{ route('mahasiswa.quizzes.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        Kembali ke Daftar Kuis
                    </a>
                </div>
            </div>