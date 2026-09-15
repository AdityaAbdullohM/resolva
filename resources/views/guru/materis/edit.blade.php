<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-bold mb-6">Edit Materi: {{ $materi->judul }}</h2>
                    <form action="{{ route('dosen.kelas.materis.update', [$kelas->id, $materi->id]) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-700 shadow-md rounded-lg p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="judul" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Judul Materi</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul', $materi->judul) }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('judul')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="5" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="link_url" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Link URL (Optional)</label>
                            <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $materi->link_url) }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('link_url')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="pertemuan_number" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Pertemuan (Opsional)</label>
                            <input type="number" name="pertemuan_number" id="pertemuan_number" min="1" value="{{ old('pertemuan_number', $materi->pertemuan_number) }}" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Isi jika materi ini terkait dengan nomor pertemuan tertentu.</p>
                            @error('pertemuan_number')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="files" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Upload File(s) Baru (Opsional):</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tipe file: PDF, Word, PPT, ZIP. Maks: 10MB per file.</p>
                            <input type="file" name="files[]" id="files" multiple class="mt-1 block w-full text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-900 focus:outline-none focus:shadow-outline @error('files.*') border-red-500 @enderror @error('files') border-red-500 @enderror">
                            @error('files.*')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                            @error('files')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($materi->materiFiles->count() > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">File(s) Tersedia:</label>
                                <div class="mt-2 border dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-800">
                                    @foreach ($materi->materiFiles as $file)
                                        <div class="flex items-center justify-between py-3 border-b dark:border-gray-600 last:border-b-0">
                                            <a href="{{ route('dosen.kelas.materis.download', ['kelas' => $kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex-grow truncate font-medium">
                                                <i class="fas fa-file-alt mr-2 text-gray-400 dark:text-gray-500"></i>{{ $file->original_name }}
                                            </a>
                                            <div class="flex items-center ml-4">
                                                <input type="checkbox" name="deleted_files[]" value="{{ $file->id }}" id="delete_file_{{ $file->id }}" class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded cursor-pointer">
                                                <label for="delete_file_{{ $file->id }}" class="ml-2 text-sm font-medium text-red-600 dark:text-red-400 cursor-pointer">Hapus</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-8">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition">
                                Update Materi
                            </button>
                            <a href="{{ route('dosen.kelas.materis.index', ['kelas' => $kelas->id]) }}" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>