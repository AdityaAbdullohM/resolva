<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold mt-2">Tambah Materi Baru</h1>
                    </div>

                    <div class="bg-white dark:bg-gray-700 shadow-md rounded-lg p-6">
                        <form action="@isset($kelas){{ route('dosen.kelas.materis.store', $kelas->id) }}@else{{ route('dosen.materis.store.general') }}@endisset" method="POST" enctype="multipart/form-data">
                            @csrf
                            @php
                                $prefillJudul = request('judul') ? urldecode(request('judul')) : old('judul');
                                $prefillPertemuan = null;
                                $pertemuanReadonly = false;

                                // If a pertemuan query parameter was provided (e.g., from the pertemuan list),
                                // prefer that value and make the field readonly so it cannot be edited.
                                if ($p = request('pertemuan')) {
                                    if (preg_match('/Pertemuan\s*(\d+)/i', $p, $m2)) {
                                        $prefillPertemuan = $m2[1];
                                        $pertemuanReadonly = true;
                                    } elseif (is_numeric($p)) {
                                        $prefillPertemuan = (int) $p;
                                        $pertemuanReadonly = true;
                                    }
                                }

                                // Fallback: try to parse pertemuan number from provided judul
                                if (!$prefillPertemuan && request('judul') && preg_match('/Pertemuan\s*(\d+)/i', request('judul'), $m)) {
                                    $prefillPertemuan = $m[1];
                                }
                            @endphp
                            @isset($kelas)
                                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                                <div class="mb-4">
                                    <label for="kelas_nama" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kelas:</label>
                                    <input type="text" id="kelas_nama" value="{{ $kelas->nama }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline bg-gray-200 dark:bg-gray-600" disabled>
                                </div>
                            @else
                                <div class="mb-4">
                                    <label for="kelas_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Pilih Kelas:</label>
                                    <select name="kelas_id" id="kelas_id" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline @error('kelas_id') border-red-500 @enderror" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelasList as $kelasItem)
                                            <option value="{{ $kelasItem->id }}" {{ old('kelas_id') == $kelasItem->id ? 'selected' : '' }}>{{ $kelasItem->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endisset

                            @isset($mataPelajaran)
                                <input type="hidden" name="mata_pelajaran_id" value="{{ $mataPelajaran->id }}">
                                <div class="mb-4">
                                    <label for="mata_pelajaran_nama" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                                    <input type="text" id="mata_pelajaran_nama" value="{{ $mataPelajaran->nama }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline bg-gray-200 dark:bg-gray-600" disabled>
                                </div>
                            @else
                                <div class="mb-4">
                                    <label for="mata_pelajaran_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline @error('mata_pelajaran_id') border-red-500 @enderror" required disabled>
                                        <option value="">Pilih Mata Kuliah</option>
                                    </select>
                                    @error('mata_pelajaran_id')
                                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endisset

                            <div class="mb-4">
                                <label for="judul" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Judul Materi:</label>
                                <input type="text" name="judul" id="judul" value="{{ $prefillJudul }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline @error('judul') border-red-500 @enderror" required>
                                @error('judul')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="pertemuan_number" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nomor Pertemuan (opsional):</label>
                                <input type="number" name="pertemuan_number" id="pertemuan_number" min="1" value="{{ old('pertemuan_number', $prefillPertemuan) }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline" {{ $pertemuanReadonly ? 'readonly' : '' }}>
                                @error('pertemuan_number')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="deskripsi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Deskripsi :</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="link_url" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Link Eksternal (Opsional):</label>
                                <input type="url" name="link_url" id="link_url" value="{{ old('link_url') }}" placeholder="https://example.com" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline @error('link_url') border-red-500 @enderror">
                                @error('link_url')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="files" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Upload File(s) (Opsional):</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tipe file: PDF, Word, PPT, ZIP. Maks: 10MB per file.</p>
                                <input type="file" name="files[]" id="files" multiple class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline @error('files.*') border-red-500 @enderror @error('files') border-red-500 @enderror">
                                @error('files.*')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                                @error('files')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                    Simpan Materi
                                </button>
                                <a href="@isset($kelas){{ route('dosen.kelas.materis.index', $kelas->id) }}@else{{ route('dosen.materis.index') }}@endisset" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kelasSelect = document.getElementById('kelas_id');
            const mataPelajaranSelect = document.getElementById('mata_pelajaran_id');

            // Function to fetch and populate Mata Kuliah
            function fetchMataPelajaran(kelasId) {
                if (!kelasId) {
                    mataPelajaranSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
                    mataPelajaranSelect.disabled = true;
                    return;
                }

                mataPelajaranSelect.disabled = true;
                mataPelajaranSelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`/dosen/get-mata-kuliah-by-kelas/${kelasId}`)
                    .then(response => response.json())
                    .then(data => {
                        mataPelajaranSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
                        data.forEach(mp => {
                            const option = document.createElement('option');
                            option.value = mp.id;
                            option.textContent = mp.nama;
                            mataPelajaranSelect.appendChild(option);
                        });
                        mataPelajaranSelect.disabled = false;
                        // Pre-select old mata_pelajaran_id if it exists
                        const oldMataPelajaranId = "{{ old('mata_pelajaran_id') }}";
                        if (oldMataPelajaranId) {
                            mataPelajaranSelect.value = oldMataPelajaranId;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching Mata Kuliah:', error);
                        mataPelajaranSelect.innerHTML = '<option value="">Error loading</option>';
                        mataPelajaranSelect.disabled = true;
                    });
            }

            // Event listener for kelas selection change
            if (kelasSelect) {
                kelasSelect.addEventListener('change', function () {
                    fetchMataPelajaran(this.value);
                });

                // Initial fetch if a kelas is already selected (e.g., due to old('kelas_id'))
                if (kelasSelect.value) {
                    fetchMataPelajaran(kelasSelect.value);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
