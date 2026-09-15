<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">{{ $pengumuman->title }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <span class="font-medium {{ $pengumuman->mataPelajaran ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                {{ $pengumuman->mataPelajaran->nama ?? 'Umum' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-2 text-sm text-gray-500">
                        Oleh {{ $pengumuman->user->name }} - {{ $pengumuman->created_at->diffForHumans() }}
                    </div>

                    <div class="mt-6 prose max-w-none">
                        {!! $pengumuman->content !!}
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-start">
                    <a href="{{ route('mahasiswa.pengumumans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        &larr; Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
