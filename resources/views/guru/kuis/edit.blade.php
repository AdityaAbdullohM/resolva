<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Edit Kuis: {{ $kuis->title }}</h1>

                    <form action="{{ route('dosen.kuis.update', $kuis->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Judul Kuis:</label>
                            <input type="text" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="title" name="title" value="{{ old('title', $kuis->title) }}" required>
                            @error('title')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Deskripsi:</label>
                            <textarea class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description">{{ old('description', $kuis->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="kelas_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kelas:</label>
                            <select class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="kelas_id" name="kelas_id" required>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', $kuis->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="mata_pelajaran_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mata Kuliah:</label>
                            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Pilih Kelas Terlebih Dahulu</option>
                            </select>
                            @error('mata_pelajaran_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="start_time" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Waktu Mulai:</label>
                            <input type="datetime-local" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($kuis->start_time)->format('Y-m-d\TH:i')) }}" required>
                            @error('start_time')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="end_time" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Waktu Selesai:</label>
                            <input type="datetime-local" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($kuis->end_time)->format('Y-m-d\TH:i')) }}" required>
                            @error('end_time')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="duration" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Durasi (menit):</label>
                            <input type="number" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="duration" name="duration" value="{{ old('duration', $kuis->duration) }}" required min="1">
                            @error('duration')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('dosen.kuis.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-400">
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
    const baseUrl = "{{ route('dosen.get-mata-kuliah-by-kelas', ['kelas' => ':kelasId']) }}";

    function clearMata() {
        mataSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
        mataSelect.disabled = true;
    }

    async function loadMata(kelasId, selectedMataPelajaranId = null) {
        if (!kelasId) {
            clearMata();
            return;
        }

        mataSelect.disabled = true;
        mataSelect.innerHTML = '<option>Memuat...</option>';

        try {
            const url = baseUrl.replace(':kelasId', kelasId);
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Gagal memuat Mata Kuliah.');
            }
            const mataPelajarans = await response.json();

            mataSelect.innerHTML = '<option value="">Pilih Mata Kuliah</option>';
            mataPelajarans.forEach(mapel => {
                const option = document.createElement('option');
                option.value = mapel.id;
                option.textContent = mapel.nama;
                if (selectedMataPelajaranId && mapel.id == selectedMataPelajaranId) {
                    option.selected = true;
                }
                mataSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
            mataSelect.innerHTML = '<option value="">Gagal memuat</option>';
        } finally {
            mataSelect.disabled = false;
        }
    }

    kelasSelect.addEventListener('change', function() {
        loadMata(this.value);
    });

    const initialKelasId = '{{ old('kelas_id', $kuis->kelas_id) }}';
    const initialMataPelajaranId = '{{ old('mata_pelajaran_id', $kuis->mata_pelajaran_id) }}';
    if (initialKelasId) {
        loadMata(initialKelasId, initialMataPelajaranId);
    }
});
</script>
@endpush
</x-app-layout>