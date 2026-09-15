<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex items-center space-x-4 mb-6">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo" class="w-24 h-24 rounded-full object-cover">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-4xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ roleBadgeClass($user->role) }} capitalize mt-1">
                            {{ roleDisplay($user->role) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Informasi Pribadi</h3>
                        <div class="space-y-2 text-gray-600 dark:text-gray-300">
                            <p><strong class="text-gray-700 dark:text-gray-400">Nama:</strong> {{ $user->name }}</p>
                            <p><strong class="text-gray-700 dark:text-gray-400">Email:</strong> {{ $user->email }}</p>
                            <p><strong class="text-gray-700 dark:text-gray-400">Role:</strong> <span class="capitalize">{{ roleDisplay($user->role) }}</span></p>
                            @if ($user->role === 'siswa')
                                <p><strong class="text-gray-700 dark:text-gray-400">NIM:</strong> {{ $user->nis ?? '-' }}</p>
                                <p><strong class="text-gray-700 dark:text-gray-400">Kelas:</strong> {{ $user->kelas->nama ?? '-' }} ({{ $user->kelas->jurusan ?? '-' }})</p>
                            @elseif ($user->role === 'guru')
                                <p><strong class="text-gray-700 dark:text-gray-400">NIP:</strong> {{ $user->nip ?? '-' }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Informasi Akademik</h3>
                        <div class="space-y-2 text-gray-600 dark:text-gray-300">
                            @if ($user->role === 'guru')
                                <div>
                                    <strong class="text-gray-700 dark:text-gray-400">Kelas yang Diajar:</strong>
                                    @forelse ($user->kelasYangDiajar as $kelasDiajar)
                                        <p class="ml-4">- {{ $kelasDiajar->nama }} ({{ $kelasDiajar->jurusan }})</p>
                                    @empty
                                        <p class="ml-4 text-gray-500 dark:text-gray-500">- Belum mengajar kelas apapun.</p>
                                    @endforelse
                                </div>
                                <div class="mt-4">
                                    <strong class="text-gray-700 dark:text-gray-400">Mata Kuliah yang Diajar:</strong>
                                    @php
                                        $mataPelajaranDiajar = [];
                                        foreach ($user->kelasYangDiajar as $kelasDiajar) {
                                            if ($kelasDiajar->pivot && $kelasDiajar->pivot->mataPelajaran) {
                                                $mataPelajaranDiajar[$kelasDiajar->pivot->mataPelajaran->id] = $kelasDiajar->pivot->mataPelajaran->nama;
                                            }
                                        }
                                    @endphp
                                    @forelse ($mataPelajaranDiajar as $mp)
                                        <p class="ml-4">- {{ $mp }}</p>
                                    @empty
                                        <p class="ml-4 text-gray-500 dark:text-gray-500">- Belum mengajar Mata Kuliah apapun.</p>
                                    @endforelse
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-500">Tidak ada informasi akademik tambahan untuk peran ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Detail Sistem</h3>
                    <div class="space-y-2 text-gray-600 dark:text-gray-300">
                        <p><strong class="text-gray-700 dark:text-gray-400">Dibuat Pada:</strong> {{ $user->created_at->format('d M Y, H:i') }}</p>
                        <p><strong class="text-gray-700 dark:text-gray-400">Diperbarui Pada:</strong> {{ $user->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="flex justify-start mt-6">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                        <x-heroicon-o-pencil class="w-4 h-4 mr-2" />
                        Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>