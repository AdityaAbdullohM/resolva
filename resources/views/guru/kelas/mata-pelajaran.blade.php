<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                <i class="fas fa-book mr-2"></i> Mata Kuliah di Kelas: {{ $kelas->nama }}
            </h2>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($mataPelajaran as $mapel)
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div class="p-3 bg-blue-100 rounded-full">
                                            <i class="fas fa-book-open text-2xl text-blue-600"></i>
                                        </div>
                                        <h3 class="ml-4 text-lg font-semibold text-gray-800">{{ $mapel->nama }}</h3>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-4 h-16 overflow-hidden">
                                        {{ Str::limit($mapel->deskripsi, 100) }}
                                    </p>
                                   
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <i class="fas fa-exclamation-circle text-5xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Belum ada Mata Kuliah yang Anda ajar di kelas ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('dosen.kelas.show', $kelas) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Kelas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
