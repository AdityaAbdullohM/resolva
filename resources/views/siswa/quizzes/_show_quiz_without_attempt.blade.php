            <form id="quiz-form" action="{{ route('mahasiswa.quizzes.submit', $quiz) }}" method="POST" class="w-full max-w-full p-4 sm:p-6 space-y-4 sm:space-y-8">
                @csrf
                <input type="hidden" name="client_started_at" id="client_started_at" value="">
                @php
                    $attemptAnswers = collect();
                    if (!empty($attempt) && method_exists($attempt, 'answers')) {
                        $attemptAnswers = $attempt->answers->keyBy('quiz_question_id');
                    }
                @endphp
                @foreach ($quiz->questions as $index => $question)
                    @php $letters = ['A','B','C','D','E']; @endphp
                    @php
                        $typeClass = $question->tipe === 'pilihan_ganda' ? 'border-l-4 border-blue-400' : ($question->tipe === 'isian' ? 'border-l-4 border-yellow-400' : ($question->tipe === 'benar_salah' ? 'border-l-4 border-indigo-400' : 'border-l-4 border-gray-300'));
                    @endphp
                    <div class="bg-white dark:bg-slate-800 dark:text-gray-200 p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden {{ $typeClass }}">
                        <div class="text-base sm:text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 flex flex-col sm:flex-row sm:items-start min-w-0 w-full">
                            <span class="mr-2 mb-2 sm:mb-0">{{ $index + 1 }}.</span>
                            <div class="flex-1 min-w-0 w-full font-normal [&>p]:mb-4 [&>p:last-child]:mb-0 [&>pre]:bg-gray-800 dark:[&>pre]:bg-slate-900 [&>pre]:text-gray-100 [&>pre]:p-4 [&>pre]:rounded-md [&>pre]:overflow-x-auto [&>pre]:my-4 [&>code]:bg-gray-200 dark:[&>code]:bg-slate-700 [&>code]:text-gray-800 dark:[&>code]:text-gray-200 [&>code]:px-1.5 [&>code]:py-0.5 [&>code]:rounded">
                                @php
                                    $text = $question->pertanyaan ?? '';
                                    $javaHtml = !empty($question->pertanyaan_java) ? '<pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">' . e($question->pertanyaan_java) . '</code></pre>' : '';
                                    $mainImageHtml = !empty($question->gambar) ? '<div class="mt-3"><img src="' . e(asset('storage/' . $question->gambar)) . '" alt="Gambar Soal" class="max-w-md rounded shadow-sm"></div>' : '';

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
                                {{-- If placeholders used, do not render separate blocks below --}}
                                @if(empty($question->pertanyaan) || !\Illuminate\Support\Str::contains($question->pertanyaan, '[java]'))
                                    @if(!empty($question->pertanyaan_java) && !\Illuminate\Support\Str::contains($question->pertanyaan ?? '', '[java]'))
                                        <div class="mt-3">
                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($question->pertanyaan_java) }}</code></pre>
                                        </div>
                                    @endif
                                @endif
                                @if(empty($question->pertanyaan) || !\Illuminate\Support\Str::contains($question->pertanyaan ?? '', '[gambar]'))
                                    @if(!empty($question->gambar))
                                        <div class="mt-4">
                                            <img src="{{ asset('storage/' . $question->gambar) }}" alt="Gambar Soal" class="w-full sm:max-w-md rounded shadow-sm">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 space-y-3 sm:pl-6 sm:border-l-2 border-l-0 pl-0 border-indigo-200/80 dark:border-indigo-900/80 sm:ml-2">
                            @if ($question->tipe === 'pilihan_ganda')
                                @foreach ($question->opsi_jawaban as $key => $option)
                                    @php
                                        $isJavaOption = is_array($question->opsi_is_java) && isset($question->opsi_is_java[$key]) && $question->opsi_is_java[$key];
                                    @endphp
                                    <label for="option-{{ $question->id }}-{{ $key }}" class="flex flex-col sm:flex-row items-start gap-3 p-3 sm:p-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg transition duration-150 ease-in-out cursor-pointer min-w-0">
                                        @php
                                            $prev = $attemptAnswers->has($question->id) ? $attemptAnswers[$question->id]->answer : null;
                                        @endphp
                                        <input type="radio" name="answers[{{ $question->id }}]" id="option-{{ $question->id }}-{{ $key }}" value="{{ $key }}" class="mt-0.5 sm:mt-1 h-4 w-4 shrink-0 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded" @if((string)$prev === (string)$key) checked @endif required>
                                        <div class="flex-1 min-w-0 flex flex-col sm:flex-row items-start w-full">
                                            <div class="flex-shrink-0 mr-4">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-700 text-indigo-700 dark:text-white font-semibold flex items-center justify-center text-sm">{{ $letters[$key] ?? strtoupper(chr(65 + $key)) }}</div>
                                            </div>
                                            <div class="text-base text-gray-700 dark:text-gray-300 flex-1 min-w-0 w-full break-words">
                                                @if($isJavaOption)
                                                    <pre class="w-full box-border rounded bg-gray-800 text-xs p-2 overflow-x-auto max-w-full whitespace-pre-wrap break-words"><code class="language-java">{{ e($option) }}</code></pre>
                                                @else
                                                    {!! (new \Parsedown())->text($option) !!}
                                                @endif

                                                @if(isset($question->opsi_gambar[$key]))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $question->opsi_gambar[$key]) }}" alt="Gambar Opsi" class="w-full sm:max-w-xs rounded shadow-sm">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            @elseif ($question->tipe === 'benar_salah')
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-start p-3 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-md transition duration-150 ease-in-out">
                                        @php $prev = $attemptAnswers->has($question->id) ? $attemptAnswers[$question->id]->answer : null; @endphp
                                        <input type="radio" name="answers[{{ $question->id }}]" id="option-{{ $question->id }}-benar" value="true" class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded cursor-pointer" @if($prev === 'true') checked @endif required>
                                        <label for="option-{{ $question->id }}-benar" class="ml-3 block flex-1 text-base text-gray-700 dark:text-gray-300 cursor-pointer">Benar</label>
                                    </div>
                                    <div class="flex items-start p-3 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-md transition duration-150 ease-in-out">
                                        <input type="radio" name="answers[{{ $question->id }}]" id="option-{{ $question->id }}-salah" value="false" class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded cursor-pointer" @if($prev === 'false') checked @endif required>
                                        <label for="option-{{ $question->id }}-salah" class="ml-3 block flex-1 text-base text-gray-700 dark:text-gray-300 cursor-pointer">Salah</label>
                                    </div>
                                </div>
                            @elseif ($question->tipe === 'isian')
                                <div class="p-2">
                                    @php $prevText = $attemptAnswers->has($question->id) ? $attemptAnswers[$question->id]->answer : ''; @endphp
                                    <input type="text" name="answers[{{ $question->id }}]" value="{{ old('answers.' . $question->id, $prevText) }}" class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base text-gray-900 dark:bg-slate-900 dark:text-gray-100 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 transition duration-150 ease-in-out" placeholder="Ketik jawaban Anda di sini" required>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                    <button type="submit" class="inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-bold rounded-lg shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800 transition transform hover:-translate-y-0.5 ease-in-out duration-150 w-full sm:w-auto">
                        Kirim Jawaban
                    </button>
                </div>
            </form>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('quiz-form');
    var clientStartedInput = document.getElementById('client_started_at');
    try {
        if(clientStartedInput){
            clientStartedInput.value = new Date().toISOString();
        }
    } catch(e) {
        // ignore
    }
    if(!form) return;

    function attachHandler(){
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var submitBtn = form.querySelector('button[type="submit"]');
            var confirmAndSubmit = function(){
                Swal.fire({
                    title: 'Konfirmasi Pengumpulan',
                    text: 'Anda yakin ingin mengumpulkan jawaban? Setelah dikirim, Anda tidak bisa mengubahnya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal'
                }).then(function(result){
                    if(result.isConfirmed){
                        if(submitBtn){ submitBtn.disabled = true; submitBtn.classList.add('opacity-60','cursor-not-allowed'); }
                        form.submit();
                    }
                });
            };

            if(typeof Swal === 'undefined'){
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.onload = confirmAndSubmit;
                s.onerror = function(){ alert('Gagal memuat konfirmasi. Silakan coba lagi.'); };
                document.head.appendChild(s);
            } else {
                confirmAndSubmit();
            }
        });
    }

    attachHandler();
});
</script>
@endpush