<x-app-layout>
 

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Refleksi untuk: {{ $refleksi->problem->title }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Dibuat pada {{ $refleksi->created_at->format('d F Y, H:i') }}
                    </p>

                    <div class="mt-6 prose max-w-none">
                        {!! nl2br(e($refleksi->content)) !!}
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('mahasiswa.refleksi.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Kembali</a>
                        <a href="{{ route('mahasiswa.refleksi.edit', $refleksi) }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Ubah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
