<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Materi untuk Kelas {{ $kelas->nama }} - {{ $mataPelajaran->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Daftar Materi</h3>
                        <a href="{{ route('dosen.kelas.mata-kuliah.materis.create', [$kelas, $mataPelajaran]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Materi Baru
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($materis->isEmpty())
                        <p>Belum ada materi untuk Mata Kuliah ini.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($materis as $materi)
                                <li class="py-4">
                                    <h4 class="text-md font-semibold">{{ $materi->judul }}</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($materi->deskripsi, 100) }}</p>
                                    @forelse ($materi->materiFiles as $file)
                                        <p class="text-sm text-gray-500">File: <a href="{{ route('dosen.kelas.materis.download', ['kelas' => $kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" class="text-blue-500 hover:text-blue-700">{{ $file->original_name }}</a></p>
                                    @empty
                                        {{-- No files --}}
                                    @endforelse
                                    @if ($materi->link_url)
                                        <p class="text-sm text-gray-500">Link: <a href="{{ $materi->link_url }}" target="_blank" class="text-blue-500 hover:text-blue-700">{{ $materi->link_url }}</a></p>
                                    @endif
                                <div class="mt-2 flex space-x-2">
                                        <a href="{{ route('dosen.kelas.materis.edit', ['kelas' => $kelas->id, 'materi' => $materi->id]) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Edit
                                        </a>
                                        <form action="{{ route('dosen.kelas.materis.destroy', ['kelas' => $kelas->id, 'materi' => $materi->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this materi and all its associated files?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-600 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
