<x-app-layout>
  

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <div class="w-full md:w-auto mb-4 md:mb-0">
                            <form action="{{ route('dosen.kuis.index') }}" method="GET">
                                <div class="relative">
                                    <input type="text" name="search" placeholder="Cari kuis..." class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 rounded-full shadow-sm transition duration-150 ease-in-out" value="{{ request('search') }}">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <a href="{{ route('dosen.kuis.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <x-heroicon-o-plus class="w-4 h-4 mr-2"/>
                            Buat Kuis Baru
                        </a>
                    </div>
                    @if($quizzes->isEmpty())
                        <div class="text-center py-16">
                            <x-heroicon-o-document-plus class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">Belum ada kuis</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai buat kuis pertama Anda untuk dibagikan kepada siswa.</p>
                            <div class="mt-6">
                                <a href="{{ route('dosen.kuis.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <x-heroicon-o-plus class="w-4 h-4 mr-2"/>
                                    Buat Kuis Baru
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($quizzes as $kuis)
                                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out border border-gray-200 dark:border-gray-600">
                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $kuis->title }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ Str::limit($kuis->description, 70) }}</p>

                                        <div class="space-y-2 text-gray-700 dark:text-gray-200 text-sm">
                                            <div class="flex items-center">
                                                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-500"/>
                                                <span>Kelas: {{ $kuis->kelas->nama ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <x-heroicon-o-question-mark-circle class="w-5 h-5 mr-2 text-green-500"/>
                                                <span>Jumlah Soal: {{ $kuis->questions->count() }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <x-heroicon-o-clock class="w-5 h-5 mr-2 text-purple-500"/>
                                                <span>Durasi: {{ $kuis->duration }} menit</span>
                                            </div>
                                            <div class="flex items-center">
                                                <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-indigo-500"/>
                                                <span>Mulai: {{ \Carbon\Carbon::parse($kuis->start_time)->format('d M Y, H:i') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <x-heroicon-o-calendar-days class="w-5 h-5 mr-2 text-red-500"/>
                                                <span>Selesai: {{ \Carbon\Carbon::parse($kuis->end_time)->format('d M Y, H:i') }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end items-center space-x-2">
                                            <a href="{{ route('dosen.kuis.show', $kuis->id) }}" title="Lihat" class="inline-flex items-center justify-center w-10 h-10 rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <x-heroicon-o-eye class="w-5 h-5"/>
                                            </a>

                                            <a href="{{ route('dosen.kuis.edit', $kuis->id) }}" title="Ubah" class="inline-flex items-center justify-center w-10 h-10 rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                            </a>

                                            <a href="{{ route('dosen.kuis.questions.create', ['kuis' => $kuis->id]) }}" title="Tambah Pertanyaan" class="inline-flex items-center justify-center w-10 h-10 rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                <x-heroicon-o-plus-circle class="w-5 h-5"/>
                                            </a>

                                            <a href="{{ route('dosen.kuis.rekap', $kuis->id) }}" title="Rekap Hasil" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-sm font-medium shadow-sm hover:bg-gray-50">
                                                <x-heroicon-o-clipboard-document-list class="w-4 h-4 mr-2"/>
                                                Rekap Hasil
                                            </a>

                                            {{-- Reset button moved to kuis show page --}}

                                            <form action="{{ route('dosen.kuis.destroy', $kuis->id) }}" method="POST" class="delete-form inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus" class="inline-flex items-center justify-center w-10 h-10 rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <x-heroicon-o-trash class="w-5 h-5"/>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $quizzes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Anda tidak akan dapat mengembalikan ini!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus kuis ini?')) {
                            form.submit();
                        }
                    }
                });
            });

            const resetForms = document.querySelectorAll('.reset-form');
            resetForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Mulai ulang kuis?',
                            text: "Semua attempt siswa akan dihapus dan kuis dapat dikerjakan ulang.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, mulai ulang',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Mulai ulang kuis? Semua attempt siswa akan dihapus dan kuis dapat dikerjakan ulang.')) {
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
