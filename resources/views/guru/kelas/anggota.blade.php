<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                <i class="fas fa-users mr-2"></i> Anggota Kelas: {{ $kelas->nama }}
            </h2>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Siswa ({{ $anggota->total() }})</h3>
                       
                    </div>
                    <div class="border rounded-lg overflow-hidden">
                        <ul class="divide-y divide-gray-200">
                            @forelse ($anggota as $siswa)
                                <li class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $siswa->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $siswa->email }}</p>
                                        </div>
                                    </div>
                                    <form class="delete-student-form" action="{{ route('dosen.kelas.remove_student', ['kelas' => $kelas->id, 'student' => $siswa->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm py-1 px-3 rounded-md hover:bg-red-100 transition">
                                            <i class="fas fa-user-minus mr-1"></i> Keluarkan
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li class="p-6 text-center text-gray-500">
                                    <i class="fas fa-exclamation-circle text-4xl text-gray-300 mb-2"></i>
                                    <p>Belum ada siswa di kelas ini.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="mt-4">
                        {{ $anggota->links() }}
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    cancelButtonText: 'Batal'
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