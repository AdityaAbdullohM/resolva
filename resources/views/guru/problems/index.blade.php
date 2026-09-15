<x-app-layout>
@include('guru.problems._pbl_modal')
    <div class="bg-gray-50 dark:bg-gray-900 py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:text-4xl sm:truncate transition-colors duration-200">
                        Problem Based Learning
                    </h1>
                    <p class="mt-1 text-lg text-gray-500 dark:text-gray-400 transition-colors duration-200">
                        Kelola semua tugas yang telah Anda buat untuk semua kelas.
                    </p>
                </div>
                <div class="mt-5 flex md:mt-0 md:ml-4 items-center gap-3">
                    <a href="{{ route('dosen.groups.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-indigo-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" title="Manajemen Kelompok">
                        <x-heroicon-o-user-group class="w-5 h-5 mr-2"/>
                        Manajemen Kelompok
                    </a>
                    <a href="{{ route('dosen.problems.create.general') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <x-heroicon-s-plus class="w-5 h-5 mr-2"/>
                        Buat Tugas Baru
                    </a>
                </div>
            </div>

            <!-- PBL validation requests panel removed as requested -->

            <!-- PBL Syntax -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800/50 rounded-xl p-6 mb-8 shadow-sm transition-colors duration-200" x-data="{ activeTab: 1 }">
                <h2 class="text-xl font-bold mb-6 text-indigo-900 dark:text-indigo-300 flex items-center transition-colors duration-200">
                    <x-heroicon-s-light-bulb class="w-6 h-6 mr-2 text-indigo-500 dark:text-indigo-400 transition-colors duration-200" />
                    Sintaks Problem Based Learning (PBL)
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-6">
                    <button @click="activeTab = 1" :class="{'bg-indigo-600 text-white shadow-md': activeTab === 1, 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': activeTab !== 1}" class="px-3 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                        1. Orientasi
                    </button>
                    <button @click="activeTab = 2" :class="{'bg-indigo-600 text-white shadow-md': activeTab === 2, 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': activeTab !== 2}" class="px-3 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                        2. Organisasi
                    </button>
                    <button @click="activeTab = 3" :class="{'bg-indigo-600 text-white shadow-md': activeTab === 3, 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': activeTab !== 3}" class="px-3 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                        3. Penyelidikan
                    </button>
                    <button @click="activeTab = 4" :class="{'bg-indigo-600 text-white shadow-md': activeTab === 4, 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': activeTab !== 4}" class="px-3 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                        4. Menyajikan
                    </button>
                    <button @click="activeTab = 5" :class="{'bg-indigo-600 text-white shadow-md': activeTab === 5, 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50': activeTab !== 5}" class="px-3 py-2 text-xs md:text-sm font-medium rounded-lg transition-all duration-200">
                        5. Evaluasi
                    </button>
                </div>

            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <form action="{{ route('dosen.problems.index') }}" method="GET">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500 transition-colors duration-200"/>
                        </div>
                        <input type="search" name="search" id="search" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-700 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 sm:text-sm transition-colors duration-200" placeholder="Cari berdasarkan judul tugas..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>

            @if ($problems->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-transparent dark:border-gray-700 transition-colors duration-200">
                    <x-heroicon-o-document-magnifying-glass class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-500 transition-colors duration-200"/>
                    <h3 class="mt-4 text-xl font-semibold text-gray-800 dark:text-gray-200 transition-colors duration-200">
                        @if(request('search'))
                            Tugas Tidak Ditemukan
                        @else
                            Belum Ada Tugas
                        @endif
                    </h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400 transition-colors duration-200">
                        @if(request('search'))
                            Tidak ada tugas yang cocok dengan pencarian Anda.
                        @else
                            Anda belum membuat tugas apapun. Mulai buat tugas pertama Anda!
                        @endif
                    </p>
                    @if(!request('search'))
                        <div class="mt-6">
                            <a href="{{ route('dosen.problems.create.general') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <x-heroicon-s-plus class="w-5 h-5 mr-2"/>
                                Buat Tugas
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <!-- Problems Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($problems as $problem)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 border border-transparent dark:border-gray-700 group dark:hover:shadow-gray-900/50 h-full flex flex-col min-h-[22rem]">
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="mb-4">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight truncate pr-2 transition-colors duration-200 break-words">{{ $problem->judul }}</h3>
                                        <span class="flex-shrink-0 inline-block px-2 py-1 text-xs font-semibold leading-tight text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 rounded-full transition-colors duration-200 border border-transparent dark:border-blue-800">{{ $problem->kelas->nama }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors duration-200">{{ $problem->mataPelajaran->nama ?? 'Umum' }}</p>

                                    @php
                                        $komps = array_filter(array_map('trim', explode(',', $problem->kompetensi_java ?? '')));
                                        $visibleKomps = array_slice($komps, 0, 3);
                                        $moreCount = max(0, count($komps) - count($visibleKomps));
                                    @endphp
                                    @if(count($komps) > 0)
                                        <div class="mt-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @foreach($visibleKomps as $k)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-800 border border-yellow-200">{{ $k }}</span>
                                                @endforeach
                                                @if($moreCount > 0)
                                                    <button type="button" class="ml-1 text-xs text-yellow-700 bg-yellow-100 px-2 py-1 rounded-md" data-toggle-more="komp-{{ $problem->id }}">+{{ $moreCount }}</button>
                                                @endif
                                            </div>
                                            @if($moreCount > 0)
                                                <div id="komp-{{ $problem->id }}" class="mt-2 hidden flex-wrap gap-2">
                                                    @foreach(array_slice($komps, 3) as $k)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-800 border border-yellow-200">{{ $k }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-3 flex-1 overflow-auto">
                                    @php
                                        $pblNames = ['Orientasi','Organisasi','Penyelidikan','Menyajikan','Evaluasi'];
                                        $pblDescs = [
                                            'Pahami masalah yang diberikan dengan saksama dan baca deskripsi tugas.',
                                            'Diskusikan strategi penyelesaian, bagi tugas, dan rencanakan langkah.',
                                            'Lakukan riset, cari informasi dari berbagai sumber, dan temukan solusi.',
                                            'Susun dan kumpulkan hasil karya berupa jawaban, kode, atau laporan.',
                                            'Lakukan refleksi dan evaluasi bersama atas proses pemecahan masalah.'
                                        ];
                                    @endphp

                                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md border border-gray-100 dark:border-gray-700 max-h-40 overflow-auto">
                                        <div class="text-sm font-semibold text-indigo-700 mb-2">Sintaks PBL</div>
                                        <div class="space-y-2">
                                            @foreach(range(1,5) as $i)
                                                <div class="flex items-start justify-between bg-white dark:bg-gray-800 p-3 rounded-md border border-gray-100 dark:border-gray-700">
                                                    <div class="pr-4">
                                                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $i }}. {{ $pblNames[$i-1] }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pblDescs[$i-1] }}</div>
                                                    </div>
                                                        <div class="flex-shrink-0">
                                                            <a href="{{ route('dosen.kelas.problems.show', ['kelas' => $problem->kelas->id, 'problem' => $problem->id]) }}?stage={{ $i }}" class="px-3 py-1 text-xs rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Detail</a>
                                                        </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4 transition-colors duration-200 mt-3">
                                    <x-heroicon-s-calendar-days class="w-5 h-5 mr-2 text-red-500 dark:text-red-400 transition-colors duration-200"/>
                                    <span class="font-medium">Deadline: {{ $problem->deadline ? \Carbon\Carbon::parse($problem->deadline)->format('d M Y, H:i') : 'Tidak ada' }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 transition-colors duration-200">
                                    <x-heroicon-s-document-check class="w-5 h-5 mr-2 text-green-500 dark:text-green-400 transition-colors duration-200"/>
                                    <span class="font-medium">{{ $problem->submissions_count }} Jawaban Terkumpul</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 transition-colors duration-200">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('dosen.problems.edit', ['problem' => $problem->id]) }}" class="text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors duration-200" title="Edit">
                                        <x-heroicon-o-pencil-square class="w-6 h-6"/>
                                    </a>
                                    <a href="{{ route('dosen.problems.discussions.index', ['problem' => $problem->id]) }}" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" title="Diskusi">
                                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6"/>
                                    </a>
                                    <form action="{{ route('dosen.problems.destroy', ['problem' => $problem->id]) }}" method="POST" class="m-0 delete-problem-form" data-title="{{ $problem->judul }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200" title="Hapus">
                                            <x-heroicon-o-trash class="w-6 h-6"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10 dark:text-gray-300">
                    {{ $problems->links() }}
                </div>
            @endif
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Toggle more kompetensi buttons
        document.querySelectorAll('[data-toggle-more]').forEach(function(btn){
            btn.addEventListener('click', function(){
                var id = this.getAttribute('data-toggle-more');
                var el = document.getElementById(id);
                if(!el) return;
                el.classList.toggle('hidden');
            });
        });
        // PBL modal open handler
        document.querySelectorAll('.open-pbl-modal').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var id = this.dataset.problemId;
                var modal = document.getElementById('pbl-modal');
                var body = document.getElementById('pbl-modal-body');
                if(!modal || !body) return;
                modal.classList.remove('hidden');
                body.innerHTML = '<div class="text-center text-sm text-gray-500">Memuat...</div>';

                fetch("{{ url('/dosen/problems') }}/"+id+"/pbl-details")
                    .then(function(res){ return res.text(); })
                    .then(function(html){ body.innerHTML = html; })
                    .catch(function(){ body.innerHTML = '<div class="text-sm text-red-500">Gagal memuat data.</div>'; });
            });
        });

        

        var closeBtn = document.getElementById('pbl-modal-close');
        if(closeBtn){ closeBtn.addEventListener('click', function(){ document.getElementById('pbl-modal').classList.add('hidden'); }); }

        // delegate validation forms inside modal
        document.addEventListener('submit', function(e){
            if(e.target && e.target.matches('.pbl-validate-form')){
                e.preventDefault();
                var form = e.target;
                var action = form.action;
                var data = new FormData(form);
                fetch(action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: data })
                    .then(function(res){ return res.json(); })
                    .then(function(json){
                        if(json.success){
                            // reload modal content
                            var pid = form.dataset.problemId;
                            fetch("{{ url('/dosen/problems') }}/"+pid+"/pbl-details").then(r=>r.text()).then(html=>document.getElementById('pbl-modal-body').innerHTML = html);
                        } else {
                            alert(json.message || 'Gagal');
                        }
                    }).catch(function(){ alert('Gagal mengirim permintaan.'); });
            }
        });
        document.querySelectorAll('.delete-problem-form').forEach(function(form){
            form.addEventListener('submit', function(e){
                e.preventDefault();
                var title = form.dataset.title || 'tugas ini';
                if(typeof Swal === 'undefined'){
                    if(confirm('Hapus "'+title+'"? Tindakan ini tidak dapat dibatalkan.')) form.submit();
                    return;
                }
                Swal.fire({
                    title: 'Hapus tugas?',
                    text: 'Anda akan menghapus "' + title + '". Tindakan ini tidak bisa dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result){
                    if(result.isConfirmed){
                        form.submit();
                    }
                });
            });
        });
    });
</script>
</x-app-layout>