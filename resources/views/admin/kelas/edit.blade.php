<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Session Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-400 p-4 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="py-1"><i class="fas fa-check-circle mr-3"></i></div>
                        <div>
                            <p class="font-bold">Berhasil</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-400 p-4 rounded-md shadow-sm" role="alert">
                    <div class="flex">
                        <div class="py-1"><i class="fas fa-exclamation-triangle mr-3"></i></div>
                        <div>
                            <p class="font-bold">Gagal</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3 shadow-sm">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 truncate">{{ $kelas->nama }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $kelas->jurusan ?? 'Jurusan belum diatur' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tahun Ajaran</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ optional(optional($kelas->semester)->tahunAjaran)->tahun ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Semester</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ optional($kelas->semester)->nama ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Mahasiswa</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $enrolledStudents->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center"><i class="fas fa-info-circle text-indigo-500 mr-3"></i>Detail Kelas</h3>
                        <form action="{{ route('admin.kelas.update', $kelas->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kelas</label>
                                    <input type="text" name="nama" id="nama" value="{{ old('nama', $kelas->nama) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="jurusan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                                    <input type="text" name="jurusan" id="jurusan" value="{{ old('jurusan', $kelas->jurusan) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('jurusan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                                    <input type="text" id="semester" value="{{ optional($kelas->semester)->nama ?? 'Belum diatur' }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 shadow-sm cursor-not-allowed" readonly>
                                    <input type="hidden" name="semester_id" value="{{ $kelas->semester_id }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Ajaran</label>
                                    <input type="text" value="{{ optional(optional($kelas->semester)->tahunAjaran)->tahun ?? 'Belum diatur' }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 shadow-sm cursor-not-allowed" readonly>
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-6">
                                <a href="{{ route('admin.kelas.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4 transition duration-150">Batal</a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                                    <i class="fas fa-save mr-2"></i> Update Detail
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    @endpush
</x-app-layout>