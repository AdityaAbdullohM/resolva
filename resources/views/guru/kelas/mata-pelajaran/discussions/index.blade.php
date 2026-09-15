<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Diskusi untuk Kelas {{ $kelas->nama }} - {{ $mataPelajaran->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Daftar Diskusi</h3>
                        <a href="{{ route('dosen.kelas.mata-kuliah.discussions.create', [$kelas, $mataPelajaran]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mulai Diskusi Baru
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($discussions->isEmpty())
                        <p>Belum ada diskusi untuk Mata Kuliah ini.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($discussions as $discussion)
                                <li class="py-4">
                                    <h4 class="text-md font-semibold">
                                        <a href="{{ route('dosen.discussions.show', $discussion) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $discussion->title }}
                                        </a>
                                    </h4>
                                    <p class="text-sm text-gray-600">Problem: {{ $discussion->problem->judul }}</p>
                                    <p class="text-sm text-gray-500">Oleh: {{ $discussion->user->name }} pada {{ \Carbon\Carbon::parse($discussion->created_at)->format('d M Y, H:i') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
