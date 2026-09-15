<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex items-start justify-between mb-6 gap-4">
                        <div>
                            <a href="{{ route('dosen.problems.index') }}" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">&larr; Kembali</a>
                            <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 text-gray-900 dark:text-gray-100">Pengumpulan {{ $problem->judul }}</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelas: {{ $kelas->nama }} • {{ $kelas->mataPelajaran->first()->nama ?? 'Mata Kuliah: -' }}</p>
                        </div>

                        <div class="hidden sm:flex items-center gap-4">
                            @if(Route::has('guru.kelas.problems.submissions.create'))
                                <a href="{{ route('dosen.kelas.problems.submissions.create', [$kelas, $problem]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">Buat Catatan</a>
                            @endif
                        </div>
                    </div>

                    @php
                        $total = $submissions->count();
                        $graded = $submissions->whereNotNull('nilai')->count();
                        $ungraded = $total - $graded;
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengumpulan</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $total }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sudah Dinilai</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $graded }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum Dinilai</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $ungraded }}</p>
                        </div>
                    </div>

                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama siswa atau catatan..." class="w-full sm:w-64 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <select name="status" class="px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
                                <option value="">Semua</option>
                                <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                                <option value="ungraded" {{ request('status') == 'ungraded' ? 'selected' : '' }}>Belum Dinilai</option>
                            </select>
                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Filter</button>
                        </form>

                        <div class="text-sm text-gray-500 dark:text-gray-400">Menampilkan <span class="font-medium text-gray-900 dark:text-gray-100">{{ $total }}</span> pengumpulan</div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        @if($submissions->isEmpty())
                            <div class="py-12 text-center">
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Belum ada pengumpulan untuk tugas ini.</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tunggu sampai siswa mengumpulkan tugas atau cek pengaturan pengumpulan.</p>
                            </div>
                        @else
                            <ul class="space-y-3">
                                @foreach($submissions as $submission)
                                    <li class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold">{{ strtoupper(substr($submission->user->name ?? 'S',0,1)) }}</div>
                                            <div>
                                                <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $submission->user->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($submission->content, 120) }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            @if($submission->nilai !== null)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Nilai: {{ $submission->nilai }}</span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-400 dark:text-yellow-900 ring-1 ring-yellow-200 dark:ring-yellow-500">Belum Dinilai</span>
                                            @endif

                                            <a href="{{ route('dosen.kelas.problems.submissions.show', [$kelas, $problem, $submission]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Lihat</a>
                                        </div>

                                        @if($submission->file_path)
                                            <div class="mt-3 sm:mt-0">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Lampiran:</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if(is_array($submission->file_path))
                                                        @foreach($submission->file_path as $i => $p)
                                                            <a href="{{ route('submissions.file', $submission) }}?index={{ $i }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 underline">Unduh {{ $i+1 }}</a>
                                                        @endforeach
                                                    @else
                                                        <a href="{{ route('submissions.file', $submission) }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 underline">Unduh File</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-4">
                                @if(method_exists($submissions, 'links'))
                                    <div class="mt-2">{{ $submissions->links() }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
