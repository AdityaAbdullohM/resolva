<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $materi->judul }}</h3>
                    <p class="text-gray-600 mt-2">{{ $materi->deskripsi }}</p>

                    @if($materi->link_url)
                        <div class="mt-4">
                            <a href="{{ $materi->link_url }}" target="_blank" class="text-blue-500 hover:underline">
                                Tonton Video
                            </a>
                        </div>
                    @endif

                    @if($materi->materiFiles->count() > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold">File Materi:</h4>
                            <ul class="list-disc list-inside">
                                @foreach($materi->materiFiles as $file)
                                    <li>
                                        @php
                                            $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);
                                            $viewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                                            $isViewable = in_array(strtolower($extension), $viewableExtensions);
                                        @endphp

                                        @if($isViewable)
                                            <a href="{{ route('mahasiswa.materi.viewFile', ['kelas' => $kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" target="_blank" class="text-blue-500 hover:underline">
                                                {{ $file->file_path }} (Lihat)
                                            </a>
                                            <span class="mx-1">|</span>
                                        @endif
                                        <a href="{{ route('mahasiswa.materi.downloadFile', ['kelas' => $kelas->id, 'materi' => $materi->id, 'materiFile' => $file->id]) }}" class="text-blue-500 hover:underline">
                                            {{ $file->file_path }} (Unduh)
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
