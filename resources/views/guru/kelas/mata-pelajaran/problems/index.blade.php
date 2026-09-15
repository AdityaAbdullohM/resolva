<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tugas PBL untuk Kelas {{ $kelas->nama }} - {{ $mataPelajaran->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Daftar Tugas PBL</h3>
                        <a href="{{ route('dosen.kelas.mata-kuliah.problems.create', [$kelas, $mataPelajaran]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Tugas PBL Baru
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($problems->isEmpty())
                        <p>Belum ada tugas PBL untuk Mata Kuliah ini.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Judul
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Deskripsi
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Deadline
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($problems as $problem)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $problem->judul }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ Str::limit($problem->deskripsi, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $problem->deadline ? \Carbon\Carbon::parse($problem->deadline)->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('dosen.kelas.problems.edit', ['kelas' => $kelas->id, 'problem' => $problem->id]) }}" class="text-green-600 hover:text-green-900 p-1 rounded-md bg-green-100 hover:bg-green-200 transition duration-150 ease-in-out flex items-center">
                                                        <x-heroicon-o-pencil class="w-4 h-4 mr-1"/> Edit
                                                    </a>
                                                    <form action="{{ route('dosen.kelas.problems.destroy', ['kelas' => $kelas->id, 'problem' => $problem->id]) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded-md bg-red-100 hover:bg-red-200 transition duration-150 ease-in-out flex items-center" onclick="return confirm('Are you sure you want to delete this problem?')">
                                                            <x-heroicon-o-trash class="w-4 h-4 mr-1"/> Hapus
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('dosen.kelas.problems.submissions.index', ['kelas' => $kelas->id, 'problem' => $problem->id]) }}" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md bg-indigo-100 hover:bg-indigo-200 transition duration-150 ease-in-out flex items-center">
                                                        <x-heroicon-o-document-text class="w-4 h-4 mr-1"/> Penilaian
                                                    </a>
                                                    <a href="{{ route('dosen.problems.discussions.index', ['problem' => $problem->id]) }}" class="text-blue-600 hover:text-blue-900 p-1 rounded-md bg-blue-100 hover:bg-blue-200 transition duration-150 ease-in-out flex items-center">
                                                        <x-heroicon-o-chat-bubble-left-right class="w-4 h-4 mr-1"/> Diskusi
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
