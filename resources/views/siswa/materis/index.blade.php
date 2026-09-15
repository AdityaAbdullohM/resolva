<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-[2.5rem] shadow-xl overflow-hidden mb-8 border border-white/10 relative">
                <!-- Decorative background elements -->
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                    <svg class="absolute -top-24 -right-24 w-96 h-96 text-white" fill="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="50" />
                    </svg>
                    <svg class="absolute top-1/2 -left-24 w-64 h-64 text-white" fill="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="50" />
                    </svg>
                </div>
                
                <div class="relative p-8 sm:p-12 z-10 grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                    <div class="lg:col-span-2">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 text-white text-sm font-semibold mb-6 backdrop-blur-md border border-white/20 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Ruang Belajar
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">
                            Materi Pembelajaran
                        </h2>
                        <p class="text-white/90 text-lg sm:text-xl max-w-2xl font-light mb-8">
                            Jelajahi dan pelajari materi terbaru dengan cara yang lebih interaktif, terstruktur, dan menyenangkan.
                        </p>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="inline-flex items-center px-4 py-2 rounded-xl bg-black/20 text-white text-sm font-medium backdrop-blur-sm border border-black/10">
                                <svg class="w-5 h-5 mr-2 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Total Pertemuan: <span class="ml-2 font-bold text-lg">{{ isset($pertemuans) ? $pertemuans->count() : (isset($pertemuanList) ? count($pertemuanList) : 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:col-span-1 bg-white/10 p-6 rounded-[2rem] backdrop-blur-md border border-white/20 shadow-lg" x-data>
                        <label for="materi-search-input" class="block text-sm font-medium text-white/90 mb-2">Cari Materi</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/60 group-focus-within:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="search" 
                                name="q" 
                                placeholder="Ketikan kata kunci..." 
                                id="materi-search-input"
                                class="block w-full pl-12 pr-12 py-3.5 bg-black/20 border border-white/20 rounded-xl leading-5 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all duration-300 shadow-inner"
                                @input.debounce.300ms="window.dispatchEvent(new CustomEvent('materi-search',{detail: $event.target.value}))"
                            >
                            <button @click="window.dispatchEvent(new CustomEvent('materi-search',{detail: ''})); document.getElementById('materi-search-input').value='';" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white flex items-center justify-center p-2 rounded-lg hover:bg-white/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <button @click="window.dispatchEvent(new CustomEvent('toggle-sort'))" class="flex-1 text-sm bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl flex items-center justify-center font-medium transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                Urutkan
                            </button>
                            <button @click="window.dispatchEvent(new CustomEvent('materi-search',{detail: ''})); document.getElementById('materi-search-input').value='';" class="text-sm bg-black/20 hover:bg-black/30 text-white/90 px-4 py-2.5 rounded-xl flex items-center justify-center font-medium transition-colors border border-white/10">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            @if ($materis->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-200 dark:border-gray-700 p-16 text-center max-w-3xl mx-auto mt-12">
                    <div class="w-24 h-24 mx-auto bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Belum Ada Materi</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">
                        Materi pembelajaran untuk kelasmu saat ini belum tersedia. Silakan periksa kembali nanti atau hubungi pengajar Anda.
                    </p>
                </div>
            @else
                @php
                    // Group materi into pertemuan labels (Pertemuan X or Umum)
                    $collection = ($materis instanceof \Illuminate\Pagination\AbstractPaginator) ? $materis->getCollection() : collect($materis);
                    $groups = $collection->groupBy(function($m){
                        if (!empty($m->pertemuan_number)) {
                            return 'Pertemuan ' . $m->pertemuan_number;
                        }
                        if (preg_match('/^(Pertemuan\s*\d+)/i', $m->judul ?? '', $matches)) {
                            return $matches[1];
                        }
                        return 'Umum';
                    });
                    $pertemuanList = $groups->keys()->filter(fn($k) => strtolower($k) !== 'umum')->values()->all();
                    // counts per pertemuan number (extract number if label starts with 'Pertemuan')
                    $pertemuanCounts = [];
                    foreach($groups as $label => $items) {
                        if (preg_match('/Pertemuan\s*(\d+)/i', $label, $m)) {
                            $num = (int)$m[1];
                            $pertemuanCounts[$num] = $items->count();
                        }
                    }

                    $materisData = $collection->map(function($m){
                        $perLabel = 'Umum';
                        if (!empty($m->pertemuan_number)) {
                            $perLabel = 'Pertemuan ' . $m->pertemuan_number;
                        } elseif (preg_match('/^(Pertemuan\s*\d+)/i', $m->judul ?? '', $matches)) {
                            $perLabel = $matches[1];
                        }
                        return [
                            'id' => $m->id,
                            'judul' => $m->judul,
                            'deskripsi_short' => \Illuminate\Support\Str::limit(strip_tags($m->deskripsi), 120),
                            'mata_pelajaran' => $m->mataPelajaran->nama ?? 'Umum',
                            'kelas' => $m->kelas->nama ?? 'Umum',
                            'lampiran_count' => $m->materiFiles->count(),
                            'lampiran' => $m->materiFiles->map(function($f){
                                return ['name' => $f->original_name, 'url' => \Illuminate\Support\Facades\Storage::url($f->file_path)];
                            })->toArray(),
                            'created_at' => $m->created_at->translatedFormat('d M Y'),
                            'url' => route('mahasiswa.materi.show', ['kelas' => $m->kelas->id ?? 0, 'materi' => $m->id]),
                            'pertemuan' => $perLabel,
                        ];
                    })->toArray();

                    $showAllByDefault = $pertemuans->isEmpty();
                @endphp

            <!-- Ensure the materiIndex factory is available before Alpine initializes -->
            <script>
                window.materiIndex = function(initialItems){
                    return {
                        query: '',
                        items: initialItems || [],
                        selectedPertemuan: '',
                        showModal: false,
                        activeAttachments: [],
                        sortDesc: true,
                        showAllByDefault: @json($showAllByDefault),
                        init(){
                            window.addEventListener('materi-search', e => { 
                                this.query = e.detail || '';
                            });
                            window.addEventListener('toggle-sort', () => {
                                this.sortDesc = !this.sortDesc;
                            });
                        },
                        setPertemuan(label){
                            console.log('setPertemuan called ->', label);
                            this.selectedPertemuan = label;
                            this.query = '';
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        },
                        clearPertemuan(){
                            this.selectedPertemuan = '';
                        },
                        get filteredItems(){
                            let items = this.items.slice();
                            if(this.selectedPertemuan){
                                items = items.filter(i => (i.pertemuan || '') === this.selectedPertemuan);
                            }
                            if(this.query){
                                const q = this.query.toLowerCase();
                                items = items.filter(i => 
                                    (i.judul || '').toLowerCase().includes(q) || 
                                    (i.mata_pelajaran || '').toLowerCase().includes(q) || 
                                    (i.kelas || '').toLowerCase().includes(q)
                                );
                            }
                            items.sort((a,b) => this.sortDesc ? (b.id - a.id) : (a.id - b.id));
                            return items;
                        },
                        openAttachments(item){
                            this.activeAttachments = item.lampiran || [];
                            this.showModal = true;
                            document.body.style.overflow = 'hidden';
                        },
                        closeModal(){
                            this.showModal = false;
                            setTimeout(() => { this.activeAttachments = []; }, 300);
                            document.body.style.overflow = '';
                        }
                    }
                }
            </script>

                        <!-- Right: Pertemuan Grid (DB-driven) -->
                        <div x-data="materiIndex(@json($materisData))" x-init="init()" class="lg:col-span-3 order-first lg:order-last">

                            @if(!empty($selected))
                                @php $items = $selectedItems ?? collect(); @endphp
                                <div class="mb-6 flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $selected }}</h3>
                                    <a href="{{ route('mahasiswa.materis.index', array_diff_key(request()->query(), ['pertemuan'=>1])) }}" class="text-sm text-indigo-600 hover:underline">Kembali ke daftar pertemuan</a>
                                </div>

                                @if($items->isEmpty())
                                    <div class="text-center py-8 text-gray-500">Tidak ada materi di {{ $selected }}.</div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                        @foreach($items as $materi)
                                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex flex-col h-full">
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $materi->judul }}</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($materi->deskripsi), 120) }}</p>
                                                </div>
                                                <div class="mt-3 flex items-center justify-between">
                                                    <div class="text-xs text-gray-500">
                                                        <span class="font-medium">{{ $materi->mataPelajaran->nama ?? 'Umum' }}</span>
                                                        <span class="mx-1">•</span>
                                                        <span>{{ $materi->kelas->nama ?? 'Umum' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('mahasiswa.materi.show', ['kelas' => $materi->kelas->id ?? 0, 'materi' => $materi->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm">Buka</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            @if(empty($selected))
                            <!-- Materi List (rendered by Alpine) -->
                            <div class="mb-8" x-show="showAllByDefault || selectedPertemuan || query" x-cloak>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedPertemuan ? selectedPertemuan : (query ? 'Hasil Pencarian' : 'Daftar Materi')"></h3>
                                    <div class="flex items-center gap-2">
                                        <button @click="clearPertemuan(); query='';" class="text-sm bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded-xl">Tampilkan Semua</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 flex flex-col h-full">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2" x-text="item.judul"></h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3" x-text="item.deskripsi_short"></p>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="text-xs text-gray-500">
                                                    <span class="font-medium" x-text="item.mata_pelajaran"></span>
                                                    <span class="mx-1">•</span>
                                                    <span x-text="item.kelas"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a :href="item.url" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm">Buka</a>
                                                    <button @click="openAttachments(item)" class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-sm rounded-lg">Lampiran <span class="ml-2 text-xs text-gray-500" x-text="item.lampiran_count ? '('+item.lampiran_count+')' : ''"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            @if(!$showAllByDefault)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @php
                                    $umumCount = $groups->get('Umum') ? $groups->get('Umum')->count() : 0;
                                @endphp

                                @foreach($pertemuans as $p)
                                    @php
                                        $num = intval($p->pertemuan_number ?? 0);
                                        $label = $num ? 'Pertemuan ' . $num : 'Umum';
                                        $count = $num ? ($pertemuanCounts[$num] ?? 0) : $umumCount;
                                        $title = $p->judul ?? $p->nama ?? $label;
                                        $excerpt = isset($p->deskripsi) ? \Illuminate\Support\Str::limit(strip_tags($p->deskripsi), 150) : '';
                                        $date = isset($p->created_at) ? $p->created_at->translatedFormat('d M Y') : '';
                                    @endphp

                                    <div class="group bg-white dark:bg-gray-800 rounded-[1.5rem] border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 flex flex-col h-full overflow-hidden transform hover:-translate-y-1 relative">
                                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                        <div class="p-6 sm:p-7 flex-1 flex flex-col">
                                            <div class="flex justify-between items-start mb-4">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-sm">{{ $label }}</span>
                                                <div class="flex items-center text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-2.5 py-1.5 rounded-lg border border-gray-100 dark:border-gray-600">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $date }}</span>
                                                </div>
                                            </div>

                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $title }}</h3>

                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-6 flex-1 break-words leading-relaxed">{{ $excerpt }}</p>

                                            <div class="flex items-center justify-between mt-auto pt-5 border-t border-gray-100 dark:border-gray-700/80">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-xl border border-gray-100 dark:border-gray-600" title="Jumlah Materi">
                                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                                        </svg>
                                                        <span>{{ $count }} materi</span>
                                                    </div>
                                                </div>

                                                <a href="{{ route('mahasiswa.materis.index', array_merge(request()->query(), ['pertemuan' => $label])) }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all duration-300 focus:outline-none shadow-md hover:shadow-lg">
                                                    Lihat Materi
                                                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                    <!-- Modal Lampiran -->
                    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Background overlay -->
                            <div x-show="showModal" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0" 
                                 x-transition:enter-end="opacity-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100" 
                                 x-transition:leave-end="opacity-0" 
                                 class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" 
                                 @click="closeModal()" aria-hidden="true"></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal panel -->
                            <div x-show="showModal" 
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700">
                                
                                <div class="px-6 py-6 sm:p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center">
                                            <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl mr-4">
                                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modal-title">
                                                Lampiran Materi
                                            </h3>
                                        </div>
                                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full p-2 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <ul class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                            <template x-for="file in activeAttachments" :key="file.url">
                                                <li>
                                                    <a :href="file.url" target="_blank" class="group flex items-center p-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md" rel="noopener">
                                                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 flex items-center justify-center text-indigo-500 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-4 flex-1 min-w-0">
                                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors" x-text="file.name"></p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klik untuk mengunduh</p>
                                                        </div>
                                                    </a>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/80 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors" @click="closeModal()">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <style>
                    /* Custom Scrollbar for Modal */
                    .custom-scrollbar::-webkit-scrollbar {
                        width: 6px;
                    }
                    .custom-scrollbar::-webkit-scrollbar-track {
                        background: transparent;
                    }
                    .custom-scrollbar::-webkit-scrollbar-thumb {
                        background-color: #CBD5E1;
                        border-radius: 20px;
                    }
                    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                        background-color: #475569;
                    }
                </style>
                
                @endpush
            @endif
        </div>
    </div>
</x-app-layout>