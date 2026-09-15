<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.tahun-ajaran.update', $tahunAjaran) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran->tahun) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                            <select id="semester" name="semester" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm">
                                <option value="Ganjil" @if(old('semester', optional($tahunAjaran->semesters->first())->nama ?? '') == 'Ganjil') selected @endif>Ganjil</option>
                                <option value="Genap" @if(old('semester', optional($tahunAjaran->semesters->first())->nama ?? '') == 'Genap') selected @endif>Genap</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" required>
                                <option value="aktif" {{ old('status', $tahunAjaran->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status', $tahunAjaran->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>

                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Batal
                            </a>
                            <button form="edit-form" type="button" onclick="event.preventDefault(); document.getElementById('edit-form').submit();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                    <form id="edit-form" action="{{ route('admin.tahun-ajaran.update', $tahunAjaran) }}" method="POST" class="hidden">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tahun_ajaran" id="hidden_tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran->tahun) }}">
                        <input type="hidden" name="status" id="hidden_status" value="{{ old('status', $tahunAjaran->status) }}">
                        <input type="hidden" name="semester" id="hidden_semester" value="{{ old('semester', optional($tahunAjaran->semesters->first())->nama ?? '') }}">
                        <script>
                            document.getElementById('tahun_ajaran').addEventListener('change', function() {
                                document.getElementById('hidden_tahun_ajaran').value = this.value;
                            });
                            var statusEl = document.getElementById('status');
                            if (statusEl) {
                                statusEl.addEventListener('change', function() {
                                    document.getElementById('hidden_status').value = this.value;
                                });
                            }
                            var semesterEl = document.getElementById('semester');
                            if (semesterEl) {
                                semesterEl.addEventListener('change', function() {
                                    document.getElementById('hidden_semester').value = this.value;
                                });
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>