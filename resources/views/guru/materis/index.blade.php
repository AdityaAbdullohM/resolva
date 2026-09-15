<x-app-layout>
  

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-200">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Modern Header + Top Bar with Search and Add Button -->
                    <div class="mb-6">
                        @php
                            $selectedTop = $selectedPertemuanLabel ?? request('pertemuan');
                            $selectedCount = 0;
                            if ($selectedTop) {
                                $collectionTop = ($materis instanceof \Illuminate\Pagination\AbstractPaginator) ? $materis->getCollection() : collect($materis);
                                $selectedCount = $collectionTop->filter(function($m) use ($selectedTop) {
                                    if (!empty($m->pertemuan_number)) {
                                        return ('Pertemuan ' . $m->pertemuan_number) === $selectedTop;
                                    }
                                    if (preg_match('/^(Pertemuan\s*\d+)/i', $m->judul ?? '', $matches)) {
                                        return ($matches[1] ?? '') === $selectedTop;
                                    }
                                    return false;
                                })->count();
                            }
                        @endphp

                        <div class="rounded-lg overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white p-5 mb-4 shadow-xl">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    @if($selectedTop)
                                        <a href="{{ route('dosen.materis.index') }}" class="text-white/90 hover:text-white mr-3">&larr; Kembali</a>
                                        <h2 class="text-2xl sm:text-3xl font-extrabold">{{ $selectedTop }}</h2>
                                        <p class="text-sm opacity-90 mt-1">Menampilkan {{ $selectedCount }} materi pada {{ $selectedTop }}.</p>
                                    @else
                                        <h2 class="text-2xl sm:text-3xl font-extrabold">Materi</h2>
                                        <p class="text-sm opacity-90 mt-1">Kelola materi pembelajaran Anda dengan cepat dan rapi.</p>
                                    @endif
                                </div>
                                <div class="hidden sm:block text-sm opacity-90"></div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            @if(($selectedPertemuanLabel ?? request('pertemuan')) != 'Pertemuan 4')
                                <form action="{{ route('dosen.materis.index') }}" method="GET" class="w-full sm:w-1/2 lg:w-1/3">
                                    <div class="relative">
                                        <input type="text" name="search" placeholder="Cari materi, kelas, atau mapel..."
                                               class="w-full pl-12 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 dark:text-gray-200 transition-colors duration-200"
                                               value="{{ request('search') }}">
                                        <div class="absolute inset-y-0 left-0 flex items-center ps-3 text-indigo-600">
                                            <x-heroicon-o-magnifying-glass class="h-5 w-5"/>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            @if($selectedTop)
                                          <a href="{{ route('dosen.materis.create.general') }}?pertemuan={{ urlencode($selectedTop) }}"
                                   class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-white text-indigo-600 font-bold rounded-lg shadow hover:shadow-lg dark:bg-indigo-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition-transform transform hover:-translate-y-0.5">
                                    <x-heroicon-o-plus class="h-5 w-5 mr-2"/>
                                    Tambah Materi
                                </a>
                            @endif
                        </div>

                    @if ($materis->isEmpty() && !$selectedTop)
                        <!-- Langsung tampilkan Daftar Pertemuan ketika tidak ada materi -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold mt-2 mb-4 text-gray-800 dark:text-gray-100">Daftar Pertemuan</h3>
                                <div class="flex items-center gap-2">
                                    <button class="open-add-pertemuan group inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 focus:outline-none">
                                        <span class="mr-2 inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10 transition-transform duration-200 transform group-hover:scale-110">
                                            <x-heroicon-o-plus class="h-4 w-4 text-white transform transition-all duration-300 group-hover:rotate-12 group-hover:animate-pulse" />
                                        </span>
                                        Tambah Pertemuan
                                    </button>
                                </div>
                            </div>

                            @php
                                $pertemuanList = $pertemuanList ?? [];
                                $pertemuanCounts = $pertemuanCounts ?? [];
                            @endphp

                            @if(empty($pertemuanList) || count($pertemuanList) === 0)
                                <div class="text-center py-12">
                                    <x-heroicon-o-folder class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                                    <h4 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Belum ada pertemuan yang dibuat.</h4>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Buat pertemuan baru menggunakan tombol "Tambah Pertemuan".</p>
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($pertemuanList as $numVal)
                                        @php
                                            $key = 'Pertemuan ' . $numVal;
                                            $count = $pertemuanCounts[$numVal] ?? 0;
                                        @endphp
                                        <div class="relative">
                                            <a href="{{ route('dosen.materis.index', array_merge(request()->query(), ['pertemuan' => $key])) }}" class="block p-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 text-white hover:scale-105 transform transition-shadow shadow-lg hover:shadow-2xl">
                                                    <div class="text-sm font-medium text-white">{{ $key }}</div>
                                                    <div class="text-xs text-white/90">{{ $count }} materi</div>
                                                </a>

                                            <form method="POST" action="{{ route('dosen.materis.pertemuan.destroy', $numVal) }}" class="absolute top-2 right-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 p-1 rounded focus:outline-none bg-white/10 hover:bg-red-600/10" title="Hapus Pertemuan {{ $numVal }}">
                                                    <x-heroicon-o-trash class="h-4 w-4"/>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Pertemuan navigation + detail view -->
                        @php
                            $collection = ($materis instanceof \Illuminate\Pagination\AbstractPaginator) ? $materis->getCollection() : collect($materis);
                            $groups = $collection->groupBy(function($m) {
                                // Prefer explicit pertemuan_number column when available
                                if (!empty($m->pertemuan_number)) {
                                    return 'Pertemuan ' . $m->pertemuan_number;
                                }
                                // Fallback to parsing from judul for existing data
                                if (preg_match('/^(Pertemuan\s*\d+)/i', $m->judul ?? '', $matches)) {
                                    return $matches[1];
                                }
                                return 'Umum';
                            });
                            $pertemuanKeys = $groups->keys()->filter(fn($k) => strtolower($k) !== 'umum');
                            $selected = $selectedPertemuanLabel ?? request('pertemuan');
                        @endphp

                        @if($selected)
                            @php $items = $groups->get($selected, collect()); @endphp

                            <!-- Selected pertemuan header moved to main header -->

                            @if($items->isEmpty())
                                    <div class="text-center py-12">
                                        <div class="mx-auto w-40 h-40 rounded-full bg-white/10 flex items-center justify-center text-white/80 mb-6">
                                            <x-heroicon-o-folder class="h-10 w-10" />
                                        </div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Materi belum ditambahkan pada {{ $selected }}.</h4>
                                        <p class="text-sm text-gray-700 dark:text-white/80 mt-2">Gunakan tombol "Tambah Materi" untuk menambahkan materi pada pertemuan ini.</p>
                                    </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($items as $materi)
                                        @php
                                            $__badgeColors = ['from-indigo-500 to-purple-500','from-pink-500 to-red-500','from-yellow-400 to-orange-400','from-green-400 to-teal-500'];
                                            $__idx = $materi->id % count($__badgeColors);
                                        @endphp
                                        <article class="materi-tile relative bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group overflow-hidden border border-transparent hover:border-indigo-200">
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-indigo-500 to-purple-500"></div>
                                            <div class="p-5 pl-6 flex-grow">
                                                <div class="flex items-start justify-between">
                                                    <div class="pr-4 w-full">
                                                        <h5 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $materi->judul }}</h5>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-3">
                                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $materi->kelas->nama }}</span>
                                                            <span class="mx-1">&bull;</span>
                                                            <span>{{ optional($materi->created_at)->isoFormat('D MMM YYYY') }}</span>
                                                        </div>
                                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ Str::limit($materi->deskripsi, 180) }}</p>
                                                    </div>
                                                    <span class="inline-flex items-center text-xs font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-500 px-3 py-1 rounded-full">{{ $materi->mataPelajaran->nama ?? 'Umum' }}</span>
                                                </div>
                                            </div>
                                            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800 flex justify-between items-center gap-2 border-t">
                                                <a href="{{ route('dosen.kelas.materis.show', ['kelas' => $materi->kelas_id, 'materi' => $materi->id]) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-white bg-white hover:bg-indigo-50 px-3 py-2 rounded-lg transition-all duration-200 shadow-sm">
                                                    <x-heroicon-o-eye class="h-5 w-5"/>
                                                    Lihat
                                                </a>
                                                <a href="{{ route('dosen.kelas.materis.edit', ['kelas' => $materi->kelas_id, 'materi' => $materi->id]) }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-white bg-white hover:bg-gray-100 px-3 py-2 rounded-lg transition-all duration-200 shadow-sm">
                                                    <x-heroicon-o-pencil class="h-5 w-5"/>
                                                    Edit
                                                </a>
                                                <div class="flex items-center gap-2">
                                                    <form method="POST" action="{{ route('dosen.kelas.materis.destroy', ['kelas' => $materi->kelas_id, 'materi' => $materi->id]) }}" class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-white bg-transparent hover:bg-red-600 px-3 py-2 rounded-lg transition">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold mt-2 mb-4 text-gray-800 dark:text-gray-100">Daftar Pertemuan</h3>
                                <div class="flex items-center gap-2">
                                    <button class="open-add-pertemuan group inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 focus:outline-none">
                                        <span class="mr-2 inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10 transition-transform duration-200 transform group-hover:scale-110">
                                            <x-heroicon-o-plus class="h-4 w-4 text-white transform transition-all duration-300 group-hover:rotate-12 group-hover:animate-pulse" />
                                        </span>
                                        Tambah Pertemuan
                                    </button>
                                </div>
                            </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @php
                                    $pertemuanList = $pertemuanList ?? [];
                                    $pertemuanCounts = $pertemuanCounts ?? [];
                                @endphp

                                @foreach($pertemuanList as $numVal)
                                    @php
                                        $key = 'Pertemuan ' . $numVal;
                                        $count = $pertemuanCounts[$numVal] ?? ($groups->get($key)?->count() ?? 0);
                                    @endphp
                                    <div class="relative">
                                        <a href="{{ route('dosen.materis.index', array_merge(request()->query(), ['pertemuan' => $key])) }}" class="block p-4 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 text-white hover:scale-105 transform transition-shadow shadow-lg hover:shadow-2xl">
                                                <div class="text-sm font-medium text-white">{{ $key }}</div>
                                                <div class="text-xs text-white/90">{{ $count }} materi</div>
                                            </a>

                                        <form method="POST" action="{{ route('dosen.materis.pertemuan.destroy', $numVal) }}" class="absolute top-2 right-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 p-1 rounded focus:outline-none bg-white/10 hover:bg-red-600/10" title="Hapus Pertemuan {{ $numVal }}">
                                                <x-heroicon-o-trash class="h-4 w-4"/>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach

                                {{-- 'Umum' section removed as requested --}}
                            </div>
                        @endif

                        <!-- Pagination -->
                        <div class="mt-8 dark:text-gray-300">
                            {{ $materis->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pertemuan (selalu tersedia) -->
    <div id="addPertemuanModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 z-10">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Pertemuan</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Masukkan nomor pertemuan yang ingin dibuat.</p>
            <div class="mt-4">
                <label for="pertemuanNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor Pertemuan</label>
                <input id="pertemuanNumber" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button id="cancelAddPertemuan" type="button" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md">Batal</button>
                <button id="confirmAddPertemuan" type="button" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Buat</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteForms = document.querySelectorAll('.delete-form');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Anda yakin?',
                            text: "Materi yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal',
                            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                            color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#545454'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        })
                    });

                    // Confirmation for pertemuan delete forms
                    document.querySelectorAll('form[action*="pertemuan"]').forEach(form => {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Hapus pertemuan dan materi?',
                                text: 'Menghapus pertemuan akan menghapus semua materi di pertemuan ini secara permanen. Tindakan ini tidak dapat dibatalkan. Lanjutkan?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Ya, hapus permanen',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        })
                    });
                });

                const addBtns = document.querySelectorAll('.open-add-pertemuan');
                const modal = document.getElementById('addPertemuanModal');
                const cancelBtn = document.getElementById('cancelAddPertemuan');
                const confirmBtn = document.getElementById('confirmAddPertemuan');
                const pertemuanInput = document.getElementById('pertemuanNumber');

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    pertemuanInput.focus();
                }
                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    pertemuanInput.value = '';
                }

                if (addBtns && addBtns.length) {
                    addBtns.forEach(b => b.addEventListener('click', function () { openModal(); }));
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function () {
                        closeModal();
                    });
                }

                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function () {
                        const num = pertemuanInput.value;
                        if (!num) {
                            alert('Mohon masukkan nomor pertemuan.');
                            pertemuanInput.focus();
                            return;
                        }
                        const n = parseInt(num);
                        if (isNaN(n) || n <= 0) {
                            alert('Nomor pertemuan tidak valid');
                            pertemuanInput.focus();
                            return;
                        }

                        // Send AJAX request to create placeholder materi for this pertemuan
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch('{{ route('dosen.materis.pertemuan.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({ pertemuan_number: n })
                        }).then(res => res.json()).then(data => {
                            if (data.error) {
                                Swal.fire('Gagal', data.error, 'error');
                                return;
                            }
                            const perLabel = 'Pertemuan ' + n;
                            const targetUrl = '{{ route('dosen.materis.index') }}' + '?pertemuan=' + encodeURIComponent(perLabel);
                            Swal.fire({
                                title: 'Pertemuan berhasil dibuat',
                                text: 'Anda akan diarahkan ke pertemuan.',
                                icon: 'success',
                                confirmButtonText: 'Lanjutkan'
                            }).then(() => {
                                window.location.href = targetUrl;
                            });
                        }).catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Terjadi kesalahan saat membuat pertemuan.', 'error');
                        });
                    });
                }

                @if(session('pertemuan_created'))
                    (function(){
                        const perNum = '{{ session('pertemuan_created') }}';
                        const perLabel = 'Pertemuan ' + perNum;
                        const targetUrl = '{{ route('dosen.materis.index') }}' + '?pertemuan=' + encodeURIComponent(perLabel);
                        Swal.fire({
                            title: 'Pertemuan berhasil ditambahkan',
                            text: 'Anda akan diarahkan ke daftar materi pertemuan.',
                            icon: 'success',
                            confirmButtonText: 'Lanjutkan'
                        }).then(() => {
                            window.location.href = targetUrl;
                        });
                    })();
                @endif
            });
        </script>
    @endpush

</x-app-layout>