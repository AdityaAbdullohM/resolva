<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-200">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-8 w-full">
                        <h1 class="text-3xl font-extrabold text-left flex-1 bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-indigo-600 to-teal-400">Problem Based Learning</h1>
                        <div class="ml-4">
                            <button id="join-group-btn" type="button" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM2 14a6 6 0 1116 0v1a1 1 0 01-1 1H3a1 1 0 01-1-1v-1z" clip-rule="evenodd" />
                            </svg>
                            Gabung Kelompok
                            </button>
                        </div>
                    </div>

                    <!-- PBL Syntax -->
                    <div class="bg-gradient-to-r from-indigo-50 to-pink-50 dark:from-indigo-900/20 dark:to-pink-900/10 rounded-2xl p-6 mb-8 shadow-lg transition-all duration-300" x-data="{ activeStep: 1, unlockedStep: 1 }" x-init="() => { window.addEventListener('pbl-status-changed', e => { if(e?.detail && typeof e.detail.activeStep !== 'undefined') unlockedStep = Math.max(unlockedStep, e.detail.activeStep) }) }">
                        <h2 class="text-xl font-bold mb-6 text-indigo-900 dark:text-indigo-300 flex items-center">
                            <x-heroicon-s-light-bulb class="w-6 h-6 mr-2 text-indigo-500 dark:text-indigo-400" />
                            Sintaks Problem Based Learning (PBL)
                        </h2>

                        @php
                            $slabels = ['1. Orientasi', '2. Organisasi', '3. Penyelidikan', '4. Menyajikan', '5. Evaluasi'];
                            $sdescs = [
                                'Pahami masalah yang diberikan dengan saksama. Bacalah deskripsi tugas untuk mengetahui apa yang harus diselesaikan.',
                                'Diskusikan strategi penyelesaian dan bertanyalah apabila ada yag kurang dipahami. Rencanakan langkah-langkah yang akan diambil.',
                                'Lakukan riset, cari informasi terkait dari berbagai sumber (materi, internet), dan temukan solusi atau algoritma penyelesaian.',
                                'Susun dan kumpulkan hasil karya berupa jawaban tertulis, kode program, atau laporan sesuai instruksi tugas.',
                                'Lakukan refleksi dan evaluasi bersama atas proses pemecahan masalah. Pelajari kembali feedback atau nilai yang diberikan.'
                            ];
                        @endphp

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <button
                                    @click="if(unlockedStep &gt;= {{ $i }}) activeStep = {{ $i }}"
                                    :class="activeStep === {{ $i }} ? 'bg-gradient-to-r from-indigo-600 to-pink-500 text-white shadow-lg' : (unlockedStep &gt;= {{ $i }} ? 'bg-white/90 text-indigo-800 shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed')"
                                    class="p-2 text-sm font-medium rounded-lg transition-all duration-300 flex items-center justify-center transform hover:-translate-y-0.5 hover:scale-105"
                                    :disabled="unlockedStep &lt; {{ $i }}"
                                >
                                    <span class="flex items-center gap-2">
                                        <span>{{ $slabels[$i-1] }}</span>
                                        <span x-show="unlockedStep &lt; {{ $i }}" class="text-[10px] px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Terkunci</span>
                                    </span>
                                </button>
                            @endfor
                        </div>

                        <div class="bg-white/95 dark:bg-gray-900/60 p-5 rounded-xl shadow-inner min-h-[100px] transition-all duration-300">
                            @for($i = 1; $i <= 5; $i++)
                                <div x-show="activeStep === {{ $i }}" x-transition.keyframe>
                                    <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-300 mb-2">{{ $i }}. {{ explode(' ', $slabels[$i-1], 2)[1] ?? $slabels[$i-1] }}</h3>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $sdescs[$i-1] }}</p>
                                </div>
                            @endfor
                        </div>

                        
                    </div>

                    @if($problems->isEmpty())
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">Tidak ada tugas</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Saat ini Anda belum memiliki tugas yang harus diselesaikan.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($problems as $problem)
                                @php
                                    $deadline = $problem->deadline ? \Carbon\Carbon::parse($problem->deadline) : null;
                                    $isPastDeadline = $deadline && $deadline->isPast();
                                    // determine user's group for this problem (problem-specific or global)
                                    $groupIdForProblem = $userGroupMap[$problem->id] ?? null;
                                    // skip problem entirely if user has no group specifically for this problem
                                    if(empty($groupIdForProblem)) continue;
                                @endphp
                                <div class="bg-white/95 dark:bg-gray-900/60 rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden">
                                    <div class="p-6 flex-grow">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-sm font-medium bg-gradient-to-r from-indigo-200 to-pink-200 text-indigo-900 dark:text-indigo-200 py-1 px-3 rounded-full">{{ $problem->mataPelajaran->nama }}</span>
                                            @if($isPastDeadline)
                                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 dark:text-red-300 bg-red-200 dark:bg-red-900/50">
                                                    Telah Lewat
                                                </span>
                                            @elseif($deadline)
                                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 dark:text-green-300 bg-green-200 dark:bg-green-900/50">
                                                    Aktif
                                                </span>
                                            @else
                                                 <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-700">
                                                    Tanpa Batas
                                                </span>
                                            @endif
                                        </div>
                                        <h2 class="text-lg font-extrabold text-gray-900 dark:text-gray-100 hover:text-indigo-700 dark:hover:text-indigo-400 leading-tight">{{ $problem->judul }}</h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelas: {{ $problem->kelas->nama }}</p>

                                        @php
                                            $slabels = ['1. Orientasi', '2. Organisasi', '3. Penyelidikan', '4. Menyajikan', '5. Evaluasi'];
                                            $sdescs = [
                                                'Pahami masalah yang diberikan dengan saksama. Bacalah deskripsi tugas untuk mengetahui apa yang harus diselesaikan.',
                                                'Diskusikan strategi penyelesaian dan bertanyalah apabila ada yag kurang dipahami. Rencanakan langkah-langkah yang akan diambil.',
                                                'Lakukan riset, cari informasi terkait dari berbagai sumber (materi, internet), dan temukan solusi atau algoritma penyelesaian.',
                                                'Susun dan kumpulkan hasil karya berupa jawaban tertulis, kode program, atau laporan sesuai instruksi tugas.',
                                                'Lakukan refleksi dan evaluasi bersama atas proses pemecahan masalah. Pelajari kembali feedback atau nilai yang diberikan.'
                                            ];
                                        @endphp

                                        <div class="mt-4" x-data="pblData({{ $problem->id }}, {{ $groupIdForProblem ?? 'null' }})" x-init="startPolling(); $watch('activeStep', val => {} )">
                                            @for($s = 1; $s <= 5; $s++)
                                                <div class="border rounded-lg p-3 mb-3 transition-colors duration-150"
                                                     :class="{
                                                        'ring-2 ring-indigo-300 bg-indigo-50 dark:bg-gray-900': activeStep === {{ $s }},
                                                        'bg-white dark:bg-gray-800': activeStep !== {{ $s }}
                                                     }">
                                                            <div class="flex justify-between items-center">
                                                                <h4 class="font-semibold text-sm flex items-center gap-2 transition-colors duration-150"
                                                                    :class="{ 'text-indigo-800 dark:text-indigo-300': activeStep === {{ $s }}, 'text-gray-800 dark:text-gray-100': activeStep !== {{ $s }} }">
                                                                    @if($s === 1)
                                                                        <a x-show="activeStep >= {{ $s }}" href="{{ route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $s]) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer transition-colors duration-200" title="Buka Halaman Detail">
                                                                            {{ $slabels[$s-1] }}
                                                                        </a>
                                                                        <span x-show="activeStep < {{ $s }}">{{ $slabels[$s-1] }}</span>
                                                                    @elseif($s === 2)
                                                                        <a x-show="activeStep >= {{ $s }}" href="{{ route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $s]) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer transition-colors duration-200" title="Buka Organisasi (Stage 2)">
                                                                            {{ $slabels[$s-1] }}
                                                                        </a>
                                                                        <span x-show="activeStep < {{ $s }}">{{ $slabels[$s-1] }}</span>
                                                                    @elseif($s === 3)
                                                                        <a x-show="activeStep >= {{ $s }}" href="{{ route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $s]) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer transition-colors duration-200" title="Buka Penyelidikan (Stage 3)">
                                                                            {{ $slabels[$s-1] }}
                                                                        </a>
                                                                        <span x-show="activeStep < {{ $s }}">{{ $slabels[$s-1] }}</span>

                                                                    @elseif($s === 4)
                                                                        <a x-show="activeStep >= {{ $s }}" href="{{ route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $s]) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer transition-colors duration-200" title="Buka Menyajikan (Stage 4)">
                                                                            {{ $slabels[$s-1] }}
                                                                        </a>
                                                                        <span x-show="activeStep < {{ $s }}">{{ $slabels[$s-1] }}</span>
                                                                    @elseif($s === 5)
                                                                        <a x-show="activeStep >= {{ $s }}" href="{{ route('mahasiswa.problems.show', ['problem' => $problem->id, 'stage' => $s]) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer transition-colors duration-200" title="Buka Evaluasi (Stage 5)">
                                                                            {{ $slabels[$s-1] }}
                                                                        </a>
                                                                        <span x-show="activeStep < {{ $s }}">{{ $slabels[$s-1] }}</span>
                                                                    @else
                                                                        <span>{{ $slabels[$s-1] }}</span>
                                                                    @endif
                                                                    <span x-show="activeStep &lt; {{ $s }}" class="text-[10px] px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Terkunci</span>
                                                                </h4>
                                                                <span class="text-xs font-medium text-green-600" x-show="validatedStep >= {{ $s }}">Selesai</span>
                                                            </div>

                                                    <div class="mt-2">
                                                        <div x-show="activeStep >= {{ $s }}" x-transition class="text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $sdescs[$s-1] }}
                                                        </div>

                                                        <div x-show="activeStep &lt; {{ $s }}" class="text-sm text-gray-500 dark:text-gray-400 py-2">
                                                            KATA KUNCI
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <button
                                                            @click.prevent="requestValidation({{ $s }})"
                                                            :disabled="validatedStep >= {{ $s }} || activeStep !== {{ $s }} || awaiting[{{ $s }}]"
                                                            :class="(validatedStep < {{ $s }} && activeStep === {{ $s }} && !awaiting[{{ $s }}]) ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                                            class="px-3 py-1 rounded text-sm font-medium transition-colors duration-150"
                                                        >
                                                            <span x-show="validatedStep >= {{ $s }}">Selesai</span>
                                                            <span x-show="validatedStep < {{ $s }} && !awaiting[{{ $s }}]">Selesai</span>
                                                            <span x-show="validatedStep < {{ $s }} && awaiting[{{ $s }}]">Menunggu validasi...</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-transparent rounded-b-lg">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <x-heroicon-o-calendar class="w-5 h-5 mr-2"/>
                                            @if($deadline)
                                                <span class="{{ $isPastDeadline ? 'text-red-500 dark:text-red-400 font-semibold' : '' }}">
                                                    Deadline: {{ $deadline->isoFormat('D MMMM YYYY, HH:mm') }}
                                                </span>
                                            @else
                                                <span>Tidak ada deadline</span>
                                            @endif
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.pblData = function(problemId){
        return {
            activeStep: 1,
            validatedStep: 0,
            awaiting: {},
            problemId: problemId,
                requestValidation(step){
                if(typeof Swal === 'undefined'){
                    alert('SweetAlert tidak tersedia');
                    return;
                }
                Swal.fire({
                    title: 'Konfirmasi pengiriman',
                    text: 'Kirim permintaan validasi ke dosen untuk langkah ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal'
                }).then(async (result)=>{
                    if(result.isConfirmed){
                        try{
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            const payload = { step };
                            if(this.groupId) payload.group_id = this.groupId;

                            const res = await fetch(`/mahasiswa/problems/${this.problemId}/pbl/request-validation`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            });
                            if(res.ok){
                                this.awaiting[step] = true;
                                Swal.fire('Terkirim', 'Permintaan validasi dikirim. Tunggu validasi dosen.', 'success');
                            } else {
                                const j = await res.json().catch(()=>null);
                                Swal.fire('Gagal', (j && j.message) ? j.message : 'Gagal mengirim permintaan', 'error');
                            }
                        }catch(err){
                            Swal.fire('Gagal', 'Terjadi kesalahan jaringan', 'error');
                        }
                    }
                });
            },
            startPolling(){
                const fetchStatus = async ()=>{
                    try{
                        const q = this.groupId ? `?group_id=${this.groupId}` : '';
                        const res = await fetch(`/mahasiswa/problems/${this.problemId}/pbl/status${q}`);
                        if(!res.ok) return;
                        const j = await res.json();
                        if(j && typeof j.validated_to !== 'undefined' && j.validated_to !== null){
                            this.validatedStep = j.validated_to;
                            // Unlock the next step after the latest validated step
                            const maxStep = 5;
                            const next = (j.validated_to < maxStep) ? (j.validated_to + 1) : maxStep;
                            if(next > this.activeStep){
                                this.activeStep = next;
                                try{ window.dispatchEvent(new CustomEvent('pbl-status-changed', { detail: { problemId: this.problemId, activeStep: this.activeStep } })); }catch(e){}
                            }

                            // clear awaiting flags for steps <= validated_to
                            for(let s=1;s<=j.validated_to;s++) this.awaiting[s]=false;

                            // mark awaiting for any pending steps reported by server
                            if(Array.isArray(j.pending_steps)){
                                // set awaiting true for pending, false for others (above validated_to)
                                for(let s=1;s<=5;s++){
                                    if(s <= j.validated_to){
                                        this.awaiting[s] = false;
                                    } else {
                                        this.awaiting[s] = j.pending_steps.includes(s);
                                    }
                                }
                            }
                        }
                    }catch(e){}
                };
                
                fetchStatus(); // Call immediately
                
                // poll every 5s to check validated step
                this._pollInterval = setInterval(fetchStatus, 5000);
            },
            stopPolling(){ if(this._pollInterval) clearInterval(this._pollInterval); }
        }
    }
</script>
        <script>
            (function(){
                const btn = document.getElementById('join-group-btn');
                if(!btn) return;
                btn.addEventListener('click', function(){
                    // Navigate to kelompok listing page
                    window.location.href = '{{ route('mahasiswa.kelompok.index') }}';
                });
            })();
        </script>
