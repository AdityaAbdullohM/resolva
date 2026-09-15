<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-800 p-3 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ajaran</label>
                            <select name="tahun_ajaran" id="tahun_ajaran" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" required>
                                <option value="2025/2026" @if(old('tahun_ajaran') == '2025/2026') selected @endif>2025/2026</option>
                                <option value="2026/2027" @if(old('tahun_ajaran') == '2026/2027') selected @endif>2026/2027</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                            <select id="semester" name="semester" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" required>
                                <option value="Ganjil" @if(old('semester') == 'Ganjil') selected @endif>Ganjil</option>
                                <option value="Genap" @if(old('semester') == 'Genap') selected @endif>Genap</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Semester</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:text-sm" required>
                                <option value="aktif" @if(old('status') == 'aktif') selected @endif>Aktif</option>
                                <option value="tidak_aktif" @if(old('status') == 'tidak_aktif') selected @endif>Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>