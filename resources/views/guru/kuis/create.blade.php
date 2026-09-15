<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Buat Kuis Baru</h1>

                    <form action="{{ route('dosen.kuis.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Judul:</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Deskripsi:</label>
                            <textarea name="description" id="description" rows="4" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="start_time" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Waktu Mulai:</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <div class="mb-4">
                            <label for="end_time" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Waktu Berakhir:</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <div class="mb-4">
                            <label for="duration" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Durasi (menit):</label>
                            <input type="number" name="duration" id="duration" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" min="1" required>
                        </div>

                        <div class="mb-4">
                            <label for="kelas_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kelas:</label>
                            <select name="kelas_id" id="kelas_id" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="mata_pelajaran_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition">
                                Simpan
                            </button>
                            <a href="{{ route('dosen.kuis.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const kelasSelect = document.getElementById('kelas_id');
        const mataSelect = document.getElementById('mata_pelajaran_id');
        const baseUrl = "/dosen/get-mata-kuliah-by-kelas";

        function clearMata() {
            mataSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
            mataSelect.removeAttribute('aria-busy');
        }

        async function loadMata(kelasId, selectedId = null) {
            if (!kelasId) { clearMata(); return; }
            mataSelect.setAttribute('aria-busy', 'true');
            mataSelect.innerHTML = '<option>Memuat...</option>';
            try {
                const res = await fetch(`${baseUrl}/${kelasId}`);
                if (!res.ok) throw new Error('Network response was not ok');
                const data = await res.json();
                mataSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.nama;
                    if (selectedId && selectedId == item.id) opt.selected = true;
                    mataSelect.appendChild(opt);
                });
                mataSelect.removeAttribute('aria-busy');
                mataSelect.disabled = false;
            } catch (e) {
                console.error(e);
                mataSelect.innerHTML = '<option value="">Tidak ada Mata Kuliah</option>';
                mataSelect.removeAttribute('aria-busy');
                mataSelect.disabled = false;
            }
        }

        kelasSelect.addEventListener('change', function() {
            loadMata(this.value);
        });

        @if(old('kelas_id'))
            loadMata('{{ old('kelas_id') }}', '{{ old('mata_pelajaran_id') ?? '' }}');
            kelasSelect.value = '{{ old('kelas_id') }}';
        @endif
    });
    </script>
    @endpush
</x-app-layout>