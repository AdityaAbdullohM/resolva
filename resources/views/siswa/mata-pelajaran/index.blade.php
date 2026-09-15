<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    Mata Kuliah Saya
                </h1>
                <p class="mt-2 text-lg text-gray-500">
                    Jelajahi materi, kerjakan tugas, dan ikuti diskusi di setiap Mata Kuliah.
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
                            <!-- Subjects Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                @php
                                    $colors = [
                                        'blue' => ['bg' => 'bg-blue-500', 'hover' => 'hover:bg-blue-600', 'text' => 'text-blue-500'],
                                        'green' => ['bg' => 'bg-green-500', 'hover' => 'hover:bg-green-600', 'text' => 'text-green-500'],
                                        'yellow' => ['bg' => 'bg-yellow-500', 'hover' => 'hover:bg-yellow-600', 'text' => 'text-yellow-500'],
                                        'red' => ['bg' => 'bg-red-500', 'hover' => 'hover:bg-red-600', 'text' => 'text-red-500'],
                                        'purple' => ['bg' => 'bg-purple-500', 'hover' => 'hover:bg-purple-600', 'text' => 'text-purple-500'],
                                        'pink' => ['bg' => 'bg-pink-500', 'hover' => 'hover:bg-pink-600', 'text' => 'text-pink-500'],
                                        'indigo' => ['bg' => 'bg-indigo-500', 'hover' => 'hover:bg-indigo-600', 'text' => 'text-indigo-500'],
                                        'teal' => ['bg' => 'bg-teal-500', 'hover' => 'hover:bg-teal-600', 'text' => 'text-teal-500'],
                                    ];
                                    $colorKeys = array_keys($colors);
                                @endphp
                                @foreach ($kelas->mataPelajaran as $index => $mp)
                                    @php
                                        // Find the pivot entry to get the correct guru for this specific class-subject combination
                                        $pivot = $mp->pivot;
                                        $guru = $pivot ? \App\Models\User::find($pivot->user_id) : null;
                                        $colorKey = $colorKeys[$index % count($colorKeys)];
                                        $colorClasses = $colors[$colorKey];
                                    @endphp
                                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                                        <div class="h-2 {{ $colorClasses['bg'] }}"></div>
                                        <div class="p-6">
                                            <h3 class="text-2xl font-bold text-gray-900 truncate">{{ $mp->nama }}</h3>
                                            <p class="text-sm text-gray-500 mb-4 h-10">
                                                {{ Str::limit($mp->deskripsi, 60) }}
                                            </p>
                                            <div class="flex items-center mb-6">
                                                <x-heroicon-s-user-circle class="w-6 h-6 text-gray-400 mr-2"/>
                                                <span class="text-gray-700 font-medium">Dosen: {{ $guru->name ?? 'N/A' }}</span>
                                            </div>

                                            <div class="flex justify-between text-sm mb-6">
                                                <div class="flex items-center text-gray-600">
                                                    <x-heroicon-o-book-open class="w-5 h-5 mr-2 {{ $colorClasses['text'] }}"/>
                                                    <span class="font-semibold">{{ $mp->materis->count() }}</span>&nbsp;Materi
                                                </div>
                                                <div class="flex items-center text-gray-600">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5 mr-2 {{ $colorClasses['text'] }}"/>
                                                    <span class="font-semibold">{{ $mp->problems->count() }}</span>&nbsp;Tugas
                                                </div>
                                            </div>

                                            <div class="border-t border-gray-200 pt-4 mt-4">
                                                <h4 class="font-semibold text-gray-800 mb-2">Materi Terbaru:</h4>
                                                @if($mp->materis->count() > 0)
                                                    <ul class="space-y-2">
                                                        @foreach($mp->materis()->latest()->take(3)->get() as $materi)
                                                            <li>
                                                                <a href="{{ route('mahasiswa.materi.show', ['kelas' => $kelas->id, 'materi' => $materi->id]) }}" class="flex items-center text-sm text-gray-600 hover:text-blue-500 transition-colors">
                                                                    <x-heroicon-o-document-text class="w-4 h-4 mr-2 flex-shrink-0"/>
                                                                    <span class="truncate">{{ $materi->judul }}</span>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    @if($mp->materis->count() > 3)
                                                        <a href="{{ route('mahasiswa.mata-kuliah.show', $mp->id) }}" class="mt-2 block text-sm text-blue-500 hover:text-blue-700 font-medium">Lihat Semua Materi &rarr;</a>
                                                    @endif
                                                @else
                                                    <p class="text-sm text-gray-500">Belum ada materi.</p>
                                                @endif
                                            </div>

                                            <a href="{{ route('mahasiswa.mata-kuliah.show', $mp->id) }}" class="block w-full text-center {{ $colorClasses['bg'] }} {{ $colorClasses['hover'] }} text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 mt-6">
                                                Masuk ke Mata Kuliah
                                            </a>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>