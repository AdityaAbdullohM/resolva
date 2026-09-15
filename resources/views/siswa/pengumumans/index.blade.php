@php
use Illuminate\Support\Str;
@endphp
<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('mahasiswa.pengumumans.index') }}" method="GET" class="mb-6">
                        <div class="flex items-center max-w-lg mx-auto">
                            <input type="text" name="search" placeholder="Cari pengumuman..." value="{{ request('search') }}" class="form-input rounded-l-md shadow-sm w-full">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Cari
                            </button>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($pengumumans as $pengumuman)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                                <div class="p-6 flex-grow">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $pengumuman->title }}</h3>
                                        <span class="text-sm font-medium {{ $pengumuman->mataPelajaran ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                            {{ $pengumuman->mataPelajaran->nama ?? 'Umum' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Oleh {{ $pengumuman->user->name }} - {{ $pengumuman->created_at->diffForHumans() }}
                                    </p>
                                    <div class="mt-4 text-gray-600 prose max-w-none">
                                        {!! Str::limit($pengumuman->content, 150) !!}
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-4">
                                    <a href="{{ route('mahasiswa.pengumumans.show', $pengumuman) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Baca Selengkapnya &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500">
                                <p>Tidak ada pengumuman saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $pengumumans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
