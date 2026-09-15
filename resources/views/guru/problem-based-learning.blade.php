<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-6">Permintaan Validasi PBL</h1>

                    @if(session('success'))
                        <div class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif

                    @if($pending->isEmpty())
                        <div>Tidak ada permintaan validasi.</div>
                    @else
                        <div class="space-y-4">
                            @foreach($pending as $req)
                                <div class="p-4 bg-white dark:bg-gray-800 border rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <div class="font-semibold">{{ $req->problem->judul ?? '—' }}</div>
                                            <div class="text-sm text-gray-500">Siswa: {{ $req->user->name ?? $req->user->email }}</div>
                                            <div class="text-sm text-gray-500">Langkah: {{ $req->step }}</div>
                                            <div class="text-sm text-gray-400">Dikirim: {{ $req->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('dosen.pbl.validations.validate', $req) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded bg-green-600 text-white">Validasi</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
