<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if($refleksis->isEmpty())
                        <div class="text-center py-12">
                            <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada refleksi</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai buat refleksi untuk tugas yang telah Anda kerjakan.</p>
                        </div>
                    @else
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($refleksis as $refleksi)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <x-heroicon-o-light-bulb class="h-5 w-5 text-white" />
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Refleksi untuk <a href="{{ route('mahasiswa.problems.show', $refleksi->problem->id) }}" class="font-medium text-gray-900">{{ $refleksi->problem->title }}</a></p>
                                                        <div class="mt-2 text-sm text-gray-700">
                                                            <p>{{ Str::limit($refleksi->content, 150) }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        <time datetime="{{ $refleksi->created_at->toIso8601String() }}">{{ $refleksi->created_at->diffForHumans() }}</time>
                                                        <div class="mt-2 flex items-center space-x-2">
                                                             <a href="{{ route('mahasiswa.refleksi.show', $refleksi) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Lihat
                                                            </a>
                                                            <a href="{{ route('mahasiswa.refleksi.edit', $refleksi) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Ubah
                                                            </a>
                                                            <form action="{{ route('mahasiswa.refleksi.destroy', $refleksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus refleksi ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
