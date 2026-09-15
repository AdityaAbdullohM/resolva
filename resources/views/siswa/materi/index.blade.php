<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    Materi Pelajaran Saya
                </h1>
                <p class="mt-2 text-lg text-gray-500">
                    Akses semua materi pelajaran dari kelas yang Anda ikuti.
                </p>
            </div>

            @if ($kelasYangDiikuti->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-xl shadow-md">
                    <x-heroicon-o-academic-cap class="w-20 h-20 mx-auto text-gray-400"/>
                    <p class="mt-4 text-xl font-semibold text-gray-600">Anda Belum Terdaftar</p>
                    <p class="mt-1 text-gray-500">Saat ini Anda belum terdaftar di kelas manapun. Hubungi admin untuk info lebih lanjut.</p>
                </div>
            @else
                <div class="space-y-12">
                    @foreach ($kelasYangDiikuti as $kelas)
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-4 border-blue-500 pl-4">
                                Kelas: {{ $kelas->nama }}
                            </h2>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <ul class="divide-y divide-gray-200">
                                    @forelse ($kelas->materis->sortBy('mataPelajaran.nama') as $materi)
                                        <li>
                                            <a href="{{ route('mahasiswa.materi.show', ['kelas' => $kelas->id, 'materi' => $materi->id]) }}" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <div class="p-6">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0">
                                                            @if($materi->link_url)
                                                                <x-heroicon-s-video-camera class="w-8 h-8 text-blue-500"/>
                                                            @elseif($materi->files->count() > 0)
                                                                <x-heroicon-s-document-duplicate class="w-8 h-8 text-green-500"/>
                                                            @else
                                                                <x-heroicon-s-document-text class="w-8 h-8 text-gray-500"/>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-semibold text-gray-500">
                                                                {{ $materi->mataPelajaran->nama ?? 'Umum' }}
                                                            </div>
                                                            <h3 class="text-xl font-bold text-gray-900 truncate">{{ $materi->judul }}</h3>
                                                            <p class="text-sm text-gray-600 mt-1">
                                                                {{ Str::limit($materi->deskripsi, 150) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="p-6 text-center text-gray-500">
                                            Belum ada materi untuk kelas ini.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
