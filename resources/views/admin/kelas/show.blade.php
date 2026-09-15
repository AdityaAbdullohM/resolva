<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-4 shadow-sm">
                            <i class="fas fa-school text-white text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $kelas->nama }}</h3>
                            <p class="text-md text-gray-600 dark:text-gray-400 truncate">{{ $kelas->jurusan ?? 'Jurusan belum diatur' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tahun Ajaran</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ optional(optional($kelas->semester)->tahunAjaran)->tahun ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Semester</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ optional($kelas->semester)->nama ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Mahasiswa</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $kelas->siswa->count() }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Mapel</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $kelas->mataPelajaran->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Interface -->
            <div x-data="{ tab: 'siswa' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button @click="tab = 'siswa'" :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'siswa', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'siswa'}" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-150 ease-in-out">
                            <i class="fas fa-users mr-2"></i> Mahasiswa Terdaftar
                        </button>
                        <button @click="tab = 'mapel'" :class="{'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'mapel', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': tab !== 'mapel'}" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-150 ease-in-out">
                            <i class="fas fa-book-open mr-2"></i> Mata Kuliah
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Siswa Tab -->
                    <div x-show="tab === 'siswa'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($kelas->siswa as $siswa)
                                    <li class="p-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="flex items-center">
                                            <span class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center mr-4">
                                                <i class="fas fa-user text-gray-500 dark:text-gray-300"></i>
                                            </span>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $siswa->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $siswa->email }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-6 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-exclamation-circle text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                        <p>Belum ada mahasiswa di kelas ini.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Mata Kuliah Tab -->
                    <div x-show="tab === 'mapel'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm dark:shadow-gray-900">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mata Kuliah</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dosen Pengampu</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($kelas->mataPelajaran as $mataPelajaran)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $mataPelajaran->nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $mataPelajaran->pivot->guru->name ?? 'Belum diatur' }}
                                            </td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-exclamation-circle text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                            <p>Belum ada Mata Kuliah di kelas ini.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Alpine.js for tabs -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    @endpush
</x-app-layout>
