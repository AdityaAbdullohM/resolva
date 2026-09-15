<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header Info -->
            <div class="hero-header overflow-hidden sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-md p-4 bg-gradient-to-br from-indigo-500 to-pink-500 shadow-md">
                            <i class="fas fa-school text-white text-3xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-3xl font-extrabold text-white truncate">{{ $kelas->nama }}</h3>
                            <p class="text-md text-white/90 truncate">{{ $kelas->jurusan ?? 'Jurusan belum diatur' }}</p>
                        </div>
                    </div>
                    <div class="space-x-3 flex items-center">
                        <a href="{{ route('dosen.kelas.add_student', $kelas->id) }}" class="inline-flex items-center bg-gradient-to-r from-green-400 to-teal-500 hover:from-green-500 hover:to-teal-600 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="fas fa-user-plus mr-2"></i>
                            Tambah Siswa
                        </a>
                        <a href="{{ route('dosen.kelas.index') }}" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center p-4 rounded-lg">
                    <div class="bg-white/10 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-white/80">Tahun Ajaran</h4>
                        <p class="mt-1 text-lg font-semibold text-white">{{ optional(optional($kelas->semester)->tahunAjaran)->tahun ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-white/80">Semester</h4>
                        <p class="mt-1 text-lg font-semibold text-white">{{ optional($kelas->semester)->nama ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-white/80">Jumlah Siswa</h4>
                        <p class="mt-1 text-lg font-semibold text-white">{{ $kelas->siswa->count() }}</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-white/80">Jumlah Mapel</h4>
                        <p class="mt-1 text-lg font-semibold text-white">{{ $kelas->mataPelajaran->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            </div>

            <!-- Enrolled Students List -->
            <div id="siswa" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center"><i class="fas fa-users mr-3 text-gray-500 dark:text-gray-400"></i>Daftar Siswa</h3>
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($kelas->siswa as $siswa)
                                <li class="p-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <div class="flex items-center">
                                        @php
                                            $avatar = $siswa->profile_photo_url;
                                        @endphp
                                        <img src="{{ $avatar }}" alt="{{ $siswa->name }}" class="h-10 w-10 rounded-full object-cover mr-4">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $siswa->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $siswa->email }}</p>
                                        </div>
                                    </div>
                                   
                                </li>
                            @empty
                                <li class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-exclamation-circle text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                    <p>Belum ada siswa di kelas ini.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-student-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan mengeluarkan siswa ini dari kelas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, keluarkan!',
                    cancelButtonText: 'Batal',
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#545454'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush