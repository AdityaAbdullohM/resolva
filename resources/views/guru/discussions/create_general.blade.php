<x-app-layout>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mulai Diskusi Baru</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih tugas terkait dan tulis topik diskusi Anda.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-md dark:shadow-none rounded-lg p-6">
                <form action="{{ route('dosen.discussions.store.general') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="problem_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tugas Terkait</label>
                        <select name="problem_id" id="problem_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <option value="">Pilih Tugas</option>
                            @foreach($problems as $problem)
                                <option value="{{ $problem->id }}" {{ old('problem_id') == $problem->id ? 'selected' : '' }}>
                                    {{ $problem->judul }} (Kelas: {{ $problem->kelas->nama }})
                                </option>
                            @endforeach
                        </select>
                        @error('problem_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Judul</label>
                        <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konten</label>
                        <textarea name="content" id="content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('dosen.discussions.index') }}" class="bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                            Batal
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-sm">
                            Kirim Diskusi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>