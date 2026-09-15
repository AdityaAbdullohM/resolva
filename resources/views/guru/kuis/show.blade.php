<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $kuis->title }}</h1>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelas: <span class="font-medium">{{ $kuis->kelas->nama }}</span></div>
                            @if($kuis->description)
                                <p class="mt-3 text-gray-700 dark:text-gray-300">{{ $kuis->description }}</p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('dosen.kuis.questions.create', $kuis->id) }}" class="inline-flex items-center bg-gradient-to-r from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 text-white font-semibold py-2 px-4 rounded shadow">Tambah Pertanyaan</a>
                            <a href="{{ route('dosen.kuis.edit', $kuis->id) }}" class="inline-flex items-center bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-2 px-3 rounded">Ubah Kuis</a>
                            <form action="{{ route('dosen.kuis.reset', $kuis->id) }}" method="POST" class="reset-form">
                                @csrf
                                <button type="submit" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded">Reset Kuis</button>
                            </form>
                            <form action="{{ route('dosen.kuis.destroy', $kuis->id) }}" method="POST" class="delete-form" data-confirm-message="Apakah Anda yakin ingin menghapus kuis ini?" data-redirect-url="{{ route('dosen.kuis.index') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-3 rounded">Hapus</button>
                            </form>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        @php
                            $totalQuestions = $kuis->questions->count();
                            $totalPossible = 0.0;
                            foreach ($kuis->questions as $qq) {
                                $totalPossible += ($qq && isset($qq->nilai)) ? (float)$qq->nilai : 0.0;
                            }
                        @endphp

                        <div class="flex items-center space-x-6">
                            <div class="text-sm">
                                <div class="text-gray-500">Jumlah Pertanyaan</div>
                                <div class="text-lg font-bold text-gray-800">{{ $totalQuestions }}</div>
                            </div>
                            <div class="text-sm">
                                <div class="text-gray-500">Total Nilai</div>
                                <div class="text-lg font-bold text-indigo-600">{{ (int) round($totalPossible) }}</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <form action="{{ route('dosen.kuis.questions.import', $kuis->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                @csrf
                                <label class="text-sm text-gray-700 dark:text-gray-300">Import (CSV/XLSX):</label>
                                <input type="file" name="file" accept=".xls,.xlsx,.csv" class="ml-2 text-sm text-gray-700" required />
                                <button type="submit" class="ml-2 inline-flex items-center bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-2 px-3 rounded">Import</button>
                            </form>

                            <a href="{{ route('dosen.kuis.questions.export', $kuis->id) }}" target="_blank" class="ml-2 inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-3 rounded">Export ke Excel</a>
                        </div>
                    </div>

                    @if($kuis->questions->isEmpty())
                        <p class="text-gray-900 dark:text-gray-100">Belum ada pertanyaan untuk kuis ini.</p>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($kuis->questions as $question)
                                @php
                                    $q = $question;
                                    $typeClass = match($q->tipe) {
                                        'pilihan_ganda' => 'border-l-4 border-green-400 bg-green-50',
                                        'isian' => 'border-l-4 border-yellow-400 bg-yellow-50',
                                        'benar_salah' => 'border-l-4 border-indigo-400 bg-indigo-50',
                                        default => 'border-l-4 border-gray-300 bg-white',
                                    };
                                @endphp

                                <div class="p-4 rounded-lg shadow-sm {{ $typeClass }}">
                                    <div class="flex justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-start">
                                                <div class="mr-3 font-semibold text-gray-800">{{ $loop->iteration }}.</div>
                                                <div class="prose max-w-none text-gray-800">
                                                    @php
                                                        $text = $q->pertanyaan ?? '';
                                                    @endphp
                                                    @if(!empty($text))
                                                        @php
                                                            $val = $q->nilai ?? null;
                                                            if ($val === null) {
                                                                $displayNilai = '-';
                                                            } else {
                                                                $displayNilai = (int) round($val);
                                                            }

                                                            $mainImageHtml = '';
                                                            if (!empty($q->gambar)) {
                                                                $mainImageHtml = '<div class="mt-2"><img src="' . e(asset('storage/' . $q->gambar)) . '" alt="Gambar Pertanyaan" class="max-w-sm rounded shadow-sm"></div>';
                                                            }
                                                        @endphp

                                                        <p class="text-sm text-gray-600 mb-1">Nilai per soal: <strong class="text-gray-800">{{ $displayNilai }}</strong></p>

                                                        @php
                                                            // If text contains raw <img> tags, render it as-is.
                                                            if (\Illuminate\Support\Str::contains($text, '<img')) {
                                                                echo $text;
                                                            } else {
                                                                // Prepare replacement HTML for placeholders
                                                                $javaHtml = '';
                                                                if (!empty($q->pertanyaan_java)) {
                                                                    $javaHtml = '<pre class="rounded"><code class="language-java">' . e($q->pertanyaan_java) . '</code></pre>';
                                                                }

                                                                // Use preg_split to keep delimiters ([gambar] and [java])
                                                                $parts = preg_split('/(\[gambar\]|\[java\])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
                                                                $out = '';
                                                                foreach ($parts as $part) {
                                                                    if ($part === '[gambar]') {
                                                                        $out .= $mainImageHtml;
                                                                    } elseif ($part === '[java]') {
                                                                        $out .= $javaHtml;
                                                                    } else {
                                                                        $out .= nl2br(e($part));
                                                                    }
                                                                }

                                                                echo $out;
                                                            }
                                                        @endphp
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!empty($q->gambar) && !\Illuminate\Support\Str::contains($q->pertanyaan ?? '', '[gambar]'))
                                                <div class="mt-3">
                                                    <img src="{{ asset('storage/' . $q->gambar) }}" alt="Gambar Pertanyaan" class="max-w-sm rounded shadow-sm">
                                                </div>
                                            @endif

                                            <div class="mt-3 text-sm text-gray-600">Tipe: <span class="font-medium">{{ ucwords(str_replace('_', ' ', $q->tipe)) }}</span></div>

                                            @if ($q->tipe == 'pilihan_ganda' && is_array($q->opsi_jawaban))
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($q->opsi_jawaban as $key => $option)
                                                        @php
                                                            $isCorrect = ((string)$key === (string)$q->jawaban_benar);
                                                            $isJavaOption = is_array($q->opsi_is_java) && isset($q->opsi_is_java[$key]) && $q->opsi_is_java[$key];
                                                            $optionImage = is_array($q->opsi_gambar) && isset($q->opsi_gambar[$key]) && $q->opsi_gambar[$key] ? $q->opsi_gambar[$key] : null;
                                                        @endphp
                                                        <div class="flex items-start space-x-2">
                                                            <div class="w-6">@if ($isCorrect) <span class="text-green-600 font-bold">✔</span> @else <span class="text-gray-400">●</span> @endif</div>
                                                            <div class="text-gray-800">
                                                                @if ($optionImage)
                                                                    <img src="{{ asset('storage/' . $optionImage) }}" alt="Opsi gambar" class="max-w-sm rounded shadow-sm">
                                                                @else
                                                                    @if ($isJavaOption)
                                                                        <pre class="rounded"><code class="language-java">{{ e($option) }}</code></pre>
                                                                    @else
                                                                        {!! (new \Parsedown())->text($option ?? '') !!}
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($q->tipe == 'benar_salah')
                                                <div class="mt-2 space-y-1">
                                                    <div class="flex items-center"><span class="mr-2">●</span> <span>Benar</span></div>
                                                    <div class="flex items-center"><span class="mr-2">●</span> <span>Salah</span></div>
                                                </div>
                                            @else
                                                <div class="mt-2 text-sm text-gray-700">Jawaban benar: <span class="font-medium">{{ $q->jawaban_benar }}</span></div>
                                            @endif
                                        </div>

                                        <div class="ml-4 flex flex-col space-y-2">
                                            <a href="{{ route('dosen.questions.edit', ['question' => $q->id]) }}" class="text-sm bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-2 rounded text-center">Ubah</a>
                                            <form action="{{ route('dosen.questions.destroy', $q->id) }}" method="POST" class="delete-form" data-confirm-message="Apakah Anda yakin ingin menghapus pertanyaan ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-2 rounded w-full">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<!-- Prism for Java highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Only keep reset confirmation; delete forms will submit normally
    (function () {
        function setupResetForms() {
            const resetForms = document.querySelectorAll('.reset-form');
            resetForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Mulai ulang kuis?',
                            text: 'Semua attempt siswa akan dihapus dan kuis dapat diikuti ulang.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, mulai ulang',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Mulai ulang kuis? Semua attempt siswa akan dihapus dan kuis dapat diikuti ulang.')) {
                            form.submit();
                        }
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupResetForms);
        } else {
            setupResetForms();
        }
    })();
</script>
@endpush