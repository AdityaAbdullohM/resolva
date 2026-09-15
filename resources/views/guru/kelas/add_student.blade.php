<x-app-layout>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-4">
                            <i class="fas fa-user-plus text-white text-3xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 truncate">Tambah Siswa ke: {{ $kelas->nama }}</h3>
                            <p class="text-md text-gray-600 dark:text-gray-400 truncate">Pilih siswa yang akan dimasukkan ke kelas ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('dosen.kelas.show', $kelas->id) }}" class="inline-block bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
                <div class="mt-6">
                    <form action="{{ route('dosen.kelas.store_student', $kelas->id) }}" method="POST">
                        @csrf

                        <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-auto">
                                @forelse ($students as $student)
                                    <li class="p-4 flex items-center">
                                        <label class="flex items-center w-full cursor-pointer">
                                            <input type="checkbox" name="students[]" value="{{ $student->id }}" class="form-checkbox h-5 w-5 text-indigo-600 mr-4">
                                            @php $avatar = $student->profile_photo_url; @endphp
                                            <img src="{{ $avatar }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-full object-cover mr-4">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $student->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->email }}</p>
                                            </div>
                                        </label>
                                    </li>
                                @empty
                                    <li class="p-6 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-info-circle text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p>Semua siswa sudah terdaftar di kelas ini atau tidak ada siswa tersedia.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Tambahkan Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
