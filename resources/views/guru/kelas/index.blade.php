<x-app-layout>

    <div class="py-8 bg-gradient-to-br from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-transparent">
                <div class="p-2 sm:p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 transition-colors duration-200">Kelas Saya</h2>
                        <div class="flex items-center space-x-4 mt-4 md:mt-0">
                            <div class="w-full md:w-auto">
                                <form action="{{ route('dosen.kelas.index') }}" method="GET">
                                    <div class="relative">
                                        <input type="text" name="search" placeholder="Cari kelas..." class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-full shadow-sm transition-all duration-200 ease-in-out" value="{{ request('search') }}">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500 transition-colors duration-200"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="md:ml-4">
                                <button type="button" id="openAddKelas" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2"/>
                                    Tambah Kelas
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-50 dark:bg-green-900/50 border-l-4 border-green-400 dark:border-green-500 text-green-700 dark:text-green-400 p-4 rounded-lg mb-6 shadow-md transition-colors duration-200" role="alert">
                            <div class="flex">
                                <div class="py-1"><x-heroicon-o-check-circle class="h-6 w-6 text-green-500 dark:text-green-400 mr-3"/></div>
                                <div>
                                    <p class="font-bold">Sukses</p>
                                    <p class="text-sm">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($kelasList->isEmpty())
                        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl shadow-md transition-colors duration-200 border border-transparent dark:border-gray-700">
                            <x-heroicon-o-academic-cap class="mx-auto h-20 w-20 text-blue-400 dark:text-blue-500 transition-colors duration-200" />
                            <h3 class="mt-6 text-2xl font-semibold text-gray-800 dark:text-gray-200 transition-colors duration-200">Belum ada kelas</h3>
                            <p class="mt-2 text-gray-500 dark:text-gray-400 transition-colors duration-200">Anda belum membuat atau ditugaskan ke kelas manapun.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($kelasList as $kela)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-all duration-300 ease-in-out hover:shadow-2xl dark:hover:shadow-gray-900/50 border border-transparent dark:border-gray-700 group">
                                    <div class="p-6">
                                        <div class="flex flex-wrap justify-between items-start gap-2">
                                            <div class="flex-grow">
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1 transition-colors duration-200">{{ $kela->nama }}</h3>
                                                <p class="text-sm text-blue-600 dark:text-blue-400 font-semibold transition-colors duration-200">{{ $kela->jurusan }}</p>
                                            </div>
                                            <div class="flex flex-wrap gap-1 justify-end">
                                                @foreach($kela->mataPelajaran as $mataPelajaran)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 transition-colors duration-200 border border-transparent dark:border-blue-800">
                                                        {{ $mataPelajaran->nama }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 transition-colors duration-200">
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 transition-colors duration-200">
                                                <x-heroicon-o-calendar-days class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500 transition-colors duration-200"/>
                                                <span>{{ $kela->semester->tahunAjaran->tahun ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-2 transition-colors duration-200">
                                                <x-heroicon-o-clipboard-document-list class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500 transition-colors duration-200"/>
                                                <span>Semester {{ $kela->semester->nama ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-6">
                                            <a href="{{ route('dosen.kelas.show', $kela->id) }}" class="w-full flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 dark:from-blue-600 dark:to-indigo-700 dark:hover:from-blue-500 dark:hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 ease-in-out transform shadow-lg hover:shadow-xl group-hover:scale-[1.02]">
                                                <x-heroicon-o-arrow-right-circle class="w-5 h-5 mr-2"/>
                                                Masuk Kelas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-10 dark:text-gray-300">
                            {{ $kelasList->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Tambah Kelas Modal -->
<div id="addKelasModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Tambah Kelas</h3>
                <button id="closeAddKelas" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&times;</button>
            </div>

            <form action="{{ route('dosen.kelas.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                        <input type="text" name="nama" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" value="{{ old('nama') }}">
                        @error('nama') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                        <input type="text" name="jurusan" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" value="{{ old('jurusan') }}">
                        @error('jurusan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mata Kuliah</label>
                        <select name="mata_pelajaran_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataPelajaranList as $mp)
                                <option value="{{ $mp->id }}" {{ old('mata_pelajaran_id') == $mp->id ? 'selected' : '' }}>{{ $mp->nama }}</option>
                            @endforeach
                        </select>
                        @error('mata_pelajaran_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" id="cancelAddKelas" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function(){
        const openBtn = document.getElementById('openAddKelas');
        const modal = document.getElementById('addKelasModal');
        const closeBtn = document.getElementById('closeAddKelas');
        const cancelBtn = document.getElementById('cancelAddKelas');

        if(openBtn){
            openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        }
        if(closeBtn){
            closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        }
        if(cancelBtn){
            cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
        }
        // Close when clicking outside modal content
        if(modal){
            modal.addEventListener('click', (e) => {
                if(e.target === modal) modal.classList.add('hidden');
            });
        }
    })();
</script>