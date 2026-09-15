<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"
             x-data="{}"
             x-init="
                $nextTick(() => {
                    const items = $el.querySelectorAll('.card-item');
                    items.forEach((item, index) => {
                        item.style.transitionDelay = `${index * 100}ms`;
                        item.classList.remove('opacity-0', 'translate-y-4');
                    });
                })
             ">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Main Content (Left and Middle Columns) -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- 1️⃣ Welcome Section -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-400 dark:to-indigo-500">Selamat Datang Kembali, {{ $guru->name }}!</h3>
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">Ini adalah pusat kendali Anda. Pantau kemajuan, kelola kelas, dan berinteraksi dengan mahasiswa.</p>
                                </div>
                                <div class="ml-4 text-right">
                                    <button id="guru-guide-btn" type="button" class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-lg shadow hover:bg-gray-800 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-8-4a1 1 0 00-.993.883L9 7v2a1 1 0 00.883.993L10 10h2a1 1 0 00.993-.883L13 9V7a1 1 0 00-1-1H10zM9 13a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 01-1 1H10a1 1 0 01-1-1v-1z" clip-rule="evenodd"/></svg>
                                        Panduan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2️⃣ Ringkasan Kelas & Mata Kuliah -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-academic-cap class="w-6 h-6 mr-3 text-indigo-500"/>Ringkasan Kelas & Mata Kuliah</h3>
                            <div class="space-y-4">
                                @forelse($kelasDiajar as $kelas)
                                    <a href="{{ route('dosen.kelas.show', $kelas->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-[1.02] group">
                                        <div class="flex justify-between items-center">
                                            <p class="font-bold text-blue-600 dark:text-blue-400">{{ $kelas->nama }}</p>
                                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center opacity-0 group-hover:opacity-100 transition-opacity">Lihat Detail <x-heroicon-s-arrow-right class="w-4 h-4 ml-1"/></span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $kelas->mataPelajaran->pluck('nama')->join(', ') ?: 'Belum ada Mata Kuliah' }}</p>
                                    </a>
                                @empty
                                    <p class="text-center py-4 text-gray-500 dark:text-gray-400">Anda belum ditugaskan ke kelas manapun.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- 3️⃣ Ringkasan Aktivitas Mengajar -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-chart-bar class="w-6 h-6 mr-3 text-indigo-500"/>Ringkasan Aktivitas Mengajar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/50 rounded-lg transition-all transform hover:scale-105 hover:shadow-lg hover:bg-blue-100 dark:hover:bg-blue-900">
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $materisCount }}</p>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah Materi</p>
                                </div>
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg transition-all transform hover:scale-105 hover:shadow-lg hover:bg-yellow-100 dark:hover:bg-yellow-900">
                                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $activeProblemsCount }}</p>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tugas/PBL Aktif</p>
                                </div>
                                <div class="p-4 bg-red-50 dark:bg-red-900/50 rounded-lg transition-all transform hover:scale-105 hover:shadow-lg hover:bg-red-100 dark:hover:bg-red-900">
                                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $tugasBelumDinilaiCount }}</p>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tugas Belum Dinilai</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5️⃣ Daftar Tugas Perlu Tindakan -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-exclamation-triangle class="w-6 h-6 mr-3 text-indigo-500"/>Daftar Tugas Perlu Tindakan</h3>
                            <div class="space-y-3">
                                @forelse($tugasPerluTindakan as $tugas)
                                    <a href="{{ route('dosen.submissions.index', ['problem_id' => $tugas->id]) }}" class="flex items-center justify-between p-4 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all duration-300 group">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 p-2 bg-red-100 dark:bg-red-900/50 rounded-full mr-4">
                                                <x-heroicon-o-pencil-square class="w-6 h-6 text-red-600 dark:text-red-400"/>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-gray-200">Nilai Tugas: {{ Str::limit($tugas->judul, 30) }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tugas->submissions_to_grade_count }} jawaban perlu dinilai.</p>
                                            </div>
                                        </div>
                                        <x-heroicon-s-arrow-right class="w-5 h-5 text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors transform group-hover:translate-x-1"/>
                                    </a>
                                @empty
                                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <x-heroicon-o-check-circle class="w-12 h-12 mx-auto text-green-500"/>
                                        <h4 class="mt-2 text-lg font-medium text-gray-800 dark:text-gray-200">Semua Beres!</h4>
                                        <p class="text-gray-500 dark:text-gray-400">Tidak ada tindakan yang diperlukan saat ini. Kerja bagus!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Sidebar -->
                <div class="space-y-8">
                    
                    <!-- 4️⃣ Notifikasi Penting -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-bell-alert class="w-6 h-6 mr-3 text-indigo-500"/>Notifikasi Penting</h3>
                            <div class="space-y-3">
                                <a href="#" class="flex items-center p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg text-yellow-800 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900 transition-all duration-200 transform hover:scale-105">
                                    <x-heroicon-o-clock class="w-5 h-5 mr-3"/>
                                    <span class="font-medium">{{ $approachingDeadlinesCount }}</span>&nbsp;Deadline Mendekat
                                </a>
                                <a href="{{ route('dosen.submissions.index') }}" class="flex items-center p-3 bg-green-100 dark:bg-green-900/50 rounded-lg text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900 transition-all duration-200 transform hover:scale-105">
                                    <x-heroicon-o-document-arrow-up class="w-5 h-5 mr-3"/>
                                    <span class="font-medium">{{ $newSubmissionsCount }}</span>&nbsp;Tugas Baru Dikumpulkan
                                </a>
                                <a href="{{ route('dosen.discussions.index') }}" class="flex items-center p-3 bg-purple-100 dark:bg-purple-900/50 rounded-lg text-purple-800 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900 transition-all duration-200 transform hover:scale-105">
                                    <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 mr-3"/>
                                    <span class="font-medium">{{ $newDiscussionPostsCount }}</span>&nbsp;Pertanyaan Diskusi Baru
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6️⃣ Akses Cepat -->
                    
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-bolt class="w-6 h-6 mr-3 text-indigo-500"/>Akses Cepat</h3>
                            <div class="space-y-3">
                                <a href="{{ route('dosen.materis.create.general') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 transition-all transform hover:scale-105 shadow-md">
                                    <x-heroicon-o-document-plus class="w-5 h-5 mr-2"/> Tambah Materi
                                </a>
                                <a href="{{ route('dosen.problems.create.general') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 transition-all transform hover:scale-105 shadow-md">
                                    <x-heroicon-o-puzzle-piece class="w-5 h-5 mr-2"/> Buat Masalah PBL
                                </a>
                                <a href="{{ route('dosen.submissions.index') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all transform hover:scale-105">
                                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2"/> Buka Penilaian
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 7️⃣ Statistik Ringan -->
                    <div class="card-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl opacity-0 transform translate-y-4 transition-all duration-500 ease-out">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"><x-heroicon-o-presentation-chart-line class="w-6 h-6 mr-3 text-indigo-500"/>Statistik Ringan</h3>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="flex items-center justify-between py-3">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah Mahasiswa Aktif</p>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $studentsCount }}</p>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Persentase Pengumpulan</p>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $submissionPercentage }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('guru-guide-btn');
            var modal = document.getElementById('guru-guide-modal');
            var overlay = document.getElementById('guru-guide-overlay');
            var close = document.getElementById('guru-guide-close');
            var close2 = document.getElementById('guru-guide-close-2');

            function openModal(){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); }}
            function closeModal(){ if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); }}

            if(btn) btn.addEventListener('click', openModal);
            if(close) close.addEventListener('click', closeModal);
            if(close2) close2.addEventListener('click', closeModal);
        });
    </script>

    <!-- Guru Guide Modal -->
    <style>
        .guru-guide-content { scrollbar-width: thin; scrollbar-color: #06b6d4 rgba(15,23,42,0.06); }
        .guru-guide-content::-webkit-scrollbar { width: 10px; }
        .guru-guide-content::-webkit-scrollbar-track { background: rgba(15,23,42,0.03); border-radius: 9999px; }
        .guru-guide-content::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#06b6d4,#34d399); border-radius: 9999px; box-shadow: inset 0 0 0 2px rgba(255,255,255,0.06); }
        .guru-guide-content::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg,#34d399,#06b6d4); }
    </style>

    <div id="guru-guide-modal" class="fixed inset-0 z-50 hidden items-start lg:items-center justify-center overflow-auto">
        <div class="absolute inset-0 bg-black/40 pointer-events-none" id="guru-guide-overlay"></div>
        <div class="relative guru-guide-content pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-3xl w-full mx-4 p-6 z-10 max-h-[80vh] overflow-y-auto" style="-webkit-overflow-scrolling: touch; touch-action: auto;">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Panduan Cepat: Dosen</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Petunjuk singkat penggunaan fitur untuk dosen.</p>
                </div>
                <button id="guru-guide-close" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">✕</button>
            </div>

            <div class="mt-4 text-gray-700 dark:text-gray-300 space-y-3">
                <section>
                    <h5 class="font-semibold">1. Menambahkan Materi</h5>
                    <p class="text-sm">Gunakan tombol <strong>Tambah Materi</strong> di panel Akses Cepat untuk membuat modul pembelajaran baru. Isi judul, deskripsi, dan lampirkan file jika perlu.</p>
                </section>

                <section>
                    <h5 class="font-semibold">2. Membuat Tugas</h5>
                    <p class="text-sm">Buat tugas baru melalui <strong>Buat Tugas</strong>, atur instruksi, lampiran, dan tanggal deadline. Tetapkan ke kelas yang sesuai.</p>
                </section>

                <section>
                    <h5 class="font-semibold">3. Menilai Tugas</h5>
                    <p class="text-sm">Buka <strong>Penilaian</strong> untuk melihat pengumpulan mahasiswa. Beri nilai, komentar, dan publikasikan umpan balik untuk setiap siswa.</p>
                </section>

                <section>
                    <h5 class="font-semibold">4. Menanggapi Diskusi</h5>
                    <p class="text-sm">Pantau pertanyaan di halaman Diskusi. Balas langsung untuk membimbing mahasiswa dan menandai jawaban yang membantu.</p>
                </section>

                <section>
                    <h5 class="font-semibold">5. Melihat daftar mahasiswa</h5>
                    <p class="text-sm">Masuk ke halaman Kelas untuk melihat daftar mahasiswa yang terdaftar di kelas yang anda ajar.</p>
                </section>
            </div>

            <div class="mt-6 text-right">
                <button id="guru-guide-close-2" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tutup</button>
            </div>
        </div>
    </div>
</x-app-layout>
