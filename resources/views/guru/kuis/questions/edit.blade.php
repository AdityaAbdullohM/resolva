@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Ubah Pertanyaan</h1>

                <form id="question-edit-form" action="{{ route('dosen.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="pertanyaan" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pertanyaan (teks) <span class="text-red-500">*</span>:</label>

                        <textarea name="pertanyaan" id="pertanyaan" rows="4" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan pertanyaan dalam teks biasa (wajib)" required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
                        @error('pertanyaan')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror

                        <p class="text-gray-600 dark:text-gray-400 text-xs italic mt-2">Opsional: Anda dapat menambahkan potongan kode Java terpisah di bawah untuk pratinjau syntax-highlighted.</p>

                        <div id="pertanyaan-preview-wrapper" class="mt-4 p-4 border rounded bg-gray-50 dark:bg-gray-900">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pratinjau Pertanyaan</h3>
                            <div id="pertanyaan-preview" class="prose prose-sm dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-200"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="pertanyaan_java" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kode Java (opsional):</label>
                        <textarea name="pertanyaan_java" id="pertanyaan_java" rows="6" class="font-mono shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan potongan kode Java di sini...">{{ old('pertanyaan_java', $question->pertanyaan_java) }}</textarea>
                        @error('pertanyaan_java')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="gambar" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Gambar (opsional):</label>
                        <input type="file" name="gambar" id="gambar" accept="image/*" class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('gambar')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror

                        <div id="gambar-preview" class="mt-3">
                            @if(!empty($question->gambar))
                                <img src="{{ asset('storage/' . $question->gambar) }}" alt="Preview" class="max-w-xs rounded">
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="tipe" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tipe Pertanyaan:</label>
                        <select name="tipe" id="tipe" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="pilihan_ganda" @if(old('tipe', $question->tipe) == 'pilihan_ganda') selected @endif>Pilihan Ganda</option>
                            <option value="isian" @if(old('tipe', $question->tipe) == 'isian') selected @endif>Isian</option>
                        </select>
                        @error('tipe')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="opsi-jawaban-container" class="mb-4 @if(old('tipe', $question->tipe) != 'pilihan_ganda') hidden @endif">
                        <p class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Opsi Jawaban (Pilihan Ganda - A s/d E):</p>

                        @php
                            $letters = ['a'=>'A', 'b'=>'B', 'c'=>'C', 'd'=>'D', 'e'=>'E'];
                        @endphp

                        @foreach($letters as $key => $label)
                            @php
                                $index = array_search($key, array_keys($letters));
                                $val = old('opsi.'.$key, is_array($question->opsi_jawaban) && isset($question->opsi_jawaban[$index]) ? $question->opsi_jawaban[$index] : '');
                                $isJavaChecked = old('opsi_is_java.'.$key, isset($question->opsi_is_java[$index]) && $question->opsi_is_java[$index]);
                            @endphp
                            <div class="mb-3 border rounded p-3 bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center justify-between">
                                    <div class="w-full">
                                        <div class="flex items-center justify-between">
                                            <label class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Opsi {{ $label }} (kode):</label>
                                            <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" name="opsi_is_java[{{ $key }}]" value="1" class="mr-2" @if($isJavaChecked) checked @endif>
                                                <span>Kode Java</span>
                                            </label>
                                        </div>
                                        <textarea name="opsi[{{ $key }}]" rows="3" class="mt-1 font-mono shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan kode untuk opsi {{ $label }}">{{ $val }}</textarea>
                                        @error('opsi.'.$key)
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="ml-4 w-48">
                                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold">Gambar opsi {{ $label }} (opsional):</label>
                                        <input type="file" name="opsi_gambar[{{ $key }}]" id="opsi_gambar_{{ $key }}" accept="image/*" class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                        @error('opsi_gambar.'.$key)
                                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                        @enderror
                                        <div id="opsi-preview-{{ $key }}" class="mt-2">
                                            @php
                                                $idx = array_search($key, array_keys($letters));
                                                if (!empty($question->opsi_gambar) && isset($question->opsi_gambar[$idx]) && $question->opsi_gambar[$idx]) {
                                                    $p = $question->opsi_gambar[$idx];
                                                } else { $p = null; }
                                            @endphp
                                            @if(!empty($p))
                                                <img src="{{ asset('storage/' . $p) }}" alt="opsi {{ $label }}" class="max-w-xs rounded">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <p class="text-gray-600 dark:text-gray-400 text-xs italic mt-2">Pilih jawaban benar di bawah.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Jawaban Benar:</label>

                        <div id="jawaban-pg" class="@if(old('tipe', $question->tipe) != 'pilihan_ganda') hidden @endif">
                            <div class="flex items-center space-x-4 mb-2">
                                @php
                                    $lettersKeys = array_keys(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E']);
                                    $oldVal = old('jawaban_benar');
                                    if ($oldVal !== null) {
                                        $currentAnswer = $oldVal;
                                    } else {
                                        if (is_numeric($question->jawaban_benar)) {
                                            $idx = intval($question->jawaban_benar);
                                            $currentAnswer = isset($lettersKeys[$idx]) ? $lettersKeys[$idx] : null;
                                        } else {
                                            $currentAnswer = $question->jawaban_benar;
                                        }
                                    }
                                @endphp

                                @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E'] as $k => $v)
                                    @php $checked = ($currentAnswer !== null && $currentAnswer == $k); @endphp
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="jawaban_benar" value="{{ $k }}" class="form-radio" @if($checked) checked @endif>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $v }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div id="jawaban-isian" class="@if(old('tipe', $question->tipe) == 'pilihan_ganda') hidden @endif">
                            <textarea id="jawaban-isian-input" name="jawaban_benar" rows="2" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan jawaban isian">{{ old('tipe') == 'isian' ? old('jawaban_benar', $question->jawaban_benar) : ( $question->tipe == 'isian' ? $question->jawaban_benar : '' ) }}</textarea>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-xs italic mt-2">Pilih jawaban benar untuk Pilihan Ganda, atau isi jawaban untuk Isian.</p>
                        @error('jawaban_benar')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="nilai-container" class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nilai per soal:</label>

                        <div id="nilai-pg" class="@if(old('tipe', $question->tipe) != 'pilihan_ganda') hidden @endif">
                            <input type="number" id="nilai-input" name="nilai" min="0" step="1" value="{{ old('nilai', isset($question->nilai) ? $question->nilai : 1) }}" class="shadow appearance-none border dark:border-gray-600 rounded w-36 py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                            <p class="text-gray-600 dark:text-gray-400 text-xs italic mt-2">Masukkan bobot/nilai untuk pertanyaan Pilihan Ganda.</p>
                        </div>

                        <div id="nilai-isian" class="@if(old('tipe', $question->tipe) == 'pilihan_ganda') hidden @endif">
                            <p class="text-gray-600 dark:text-gray-400 text-sm italic">Nilai untuk soal Isian akan diinput manual setelah pengerjaan kuis.</p>
                            <input type="hidden" id="nilai-hidden" name="" value="{{ old('nilai', '') }}">
                        </div>

                        @error('nilai')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('dosen.kuis.show', $question->quiz_id) }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-400">
                            Selesai & Kembali ke Kuis
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<!-- Prism CSS/JS for syntax highlighting (Java) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>

<script>
    function updateFormByType(type) {
        var opsiContainer = document.getElementById('opsi-jawaban-container');
        var jawabanPg = document.getElementById('jawaban-pg');
        var jawabanIsian = document.getElementById('jawaban-isian');
        var nilaiPg = document.getElementById('nilai-pg');
        var nilaiIsian = document.getElementById('nilai-isian');
        var nilaiInput = document.getElementById('nilai-input');
        var nilaiHidden = document.getElementById('nilai-hidden');

        if (type === 'pilihan_ganda') {
            if (opsiContainer) opsiContainer.classList.remove('hidden');
            if (jawabanPg) jawabanPg.classList.remove('hidden');
            if (jawabanIsian) jawabanIsian.classList.add('hidden');
            if (nilaiPg) nilaiPg.classList.remove('hidden');
            if (nilaiIsian) nilaiIsian.classList.add('hidden');

            if (nilaiInput) { nilaiInput.disabled = false; nilaiInput.name = 'nilai'; }
            if (nilaiHidden) { nilaiHidden.name = ''; }

            document.querySelectorAll('input[type="radio"][name="jawaban_benar"]').forEach(function(r){ r.disabled = false; });
            var taj = document.getElementById('jawaban-isian-input'); if (taj) { taj.disabled = true; taj.required = false; }
        } else {
            if (opsiContainer) opsiContainer.classList.add('hidden');
            if (jawabanPg) jawabanPg.classList.add('hidden');
            if (jawabanIsian) jawabanIsian.classList.remove('hidden');
            if (nilaiPg) nilaiPg.classList.add('hidden');
            if (nilaiIsian) nilaiIsian.classList.remove('hidden');

            if (nilaiInput) { nilaiInput.disabled = true; nilaiInput.removeAttribute('name'); }
            if (nilaiHidden) { nilaiHidden.name = ''; nilaiHidden.value = ''; }

            document.querySelectorAll('input[type="radio"][name="jawaban_benar"]').forEach(function(r){ r.disabled = true; r.checked = false; });
            var taj = document.getElementById('jawaban-isian-input'); if (taj) { taj.disabled = false; taj.required = true; }
        }
    }

    var tipeEl = document.getElementById('tipe');
    if (tipeEl) {
        tipeEl.addEventListener('change', function() {
            updateFormByType(this.value);
        });
        // initialize on load
        updateFormByType(tipeEl.value);
    }

    // Preview for main question image
    var gambarInput = document.getElementById('gambar');
    if (gambarInput) {
        gambarInput.addEventListener('change', function(e) {
            var previewContainer = document.getElementById('gambar-preview');
            previewContainer.innerHTML = '';
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) return;
            var img = document.createElement('img');
            img.className = 'max-w-xs rounded';
            img.alt = 'Preview';
            var reader = new FileReader();
            reader.onload = function(ev) { img.src = ev.target.result; };
            reader.readAsDataURL(file);
            previewContainer.appendChild(img);
        });
    }

    // Preview for each opsi gambar (a-e)
    ['a','b','c','d','e'].forEach(function(letter) {
        var input = document.getElementById('opsi_gambar_' + letter);
        var preview = document.getElementById('opsi-preview-' + letter);
        if (!input || !preview) return;
        input.addEventListener('change', function(e) {
            preview.innerHTML = '';
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) return;
            var img = document.createElement('img');
            img.className = 'max-w-xs rounded';
            img.alt = 'Preview opsi ' + letter.toUpperCase();
            var reader = new FileReader();
            reader.onload = function(ev) { img.src = ev.target.result; };
            reader.readAsDataURL(file);
            preview.appendChild(img);
        });
    });

    // --- Pertanyaan preview (text or Java code) ---
    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function nl2br(str) {
        return str.replace(/\n/g, '<br>');
    }

    var pertanyaanTextarea = document.getElementById('pertanyaan');
    var pertanyaanJavaTextarea = document.getElementById('pertanyaan_java');
    var previewDiv = document.getElementById('pertanyaan-preview');

    function renderPertanyaanPreview() {
        var textVal = pertanyaanTextarea ? (pertanyaanTextarea.value || '') : '';
        var javaVal = pertanyaanJavaTextarea ? (pertanyaanJavaTextarea.value || '') : '';
        previewDiv.innerHTML = '';

        if (!textVal.trim() && !javaVal.trim()) {
            previewDiv.innerHTML = '<p class="text-gray-500">Belum ada isi pertanyaan.</p>';
            return;
        }

        if (javaVal.trim()) {
            var pre = document.createElement('pre');
            pre.className = 'rounded';
            var code = document.createElement('code');
            code.className = 'language-java';
            code.textContent = javaVal;
            pre.appendChild(code);
            previewDiv.appendChild(pre);
            if (window.Prism) Prism.highlightElement(code);
        } else {
            var p = document.createElement('div');
            p.innerHTML = nl2br(escapeHtml(textVal));
            previewDiv.appendChild(p);
        }
    }

    if (pertanyaanTextarea) {
        pertanyaanTextarea.addEventListener('input', renderPertanyaanPreview);
    }
    if (pertanyaanJavaTextarea) {
        pertanyaanJavaTextarea.addEventListener('input', renderPertanyaanPreview);
    }

    // initial render
    renderPertanyaanPreview();
</script>
@endpush
@endsection
