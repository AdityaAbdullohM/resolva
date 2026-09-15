<x-app-layout>
    <div class="py-8 md:py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 page-enter">

            <!-- 1. Header & Informasi Akademik -->
            <div class="px-4 sm:px-0">
                <div class="relative overflow-hidden rounded-[28px] border border-indigo-100/80 bg-gradient-to-br from-indigo-700 via-sky-600 to-cyan-500 p-6 md:p-8 shadow-xl dark:border-indigo-500/30">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.28),_transparent_45%)]"></div>
                    <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center rounded-full border border-white/25 bg-white/15 px-3 py-1 text-sm font-medium text-white/90 backdrop-blur">
                                <x-heroicon-s-sparkles class="mr-2 h-4 w-4" />
                                Dashboard Mahasiswa
                            </div>
                            <h1 class="mt-4 text-2xl md:text-3xl font-bold text-white">
                                Selamat Datang, <span class="text-cyan-100">{{ Auth::user()->name }}</span>!
                            </h1>
                            <p class="mt-2 text-base text-indigo-50">Jangan lewatkan progress, tenggat, dan feedback terbaru hari ini.</p>
                            <div class="mt-5 flex flex-wrap gap-3">
                                <a href="{{ route('mahasiswa.problems.index') }}" class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                                    <x-heroicon-o-play class="mr-2 h-4 w-4" />
                                    Lanjutkan PBL
                                </a>
                                <a href="{{ route('mahasiswa.quizzes.index') }}" class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                                    <x-heroicon-o-academic-cap class="mr-2 h-4 w-4" />
                                    Lihat Kuis
                                </a>
                                <button id="openPanduan" type="button" class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                                    <x-heroicon-o-question-mark-circle class="mr-2 h-4 w-4" />
                                    Panduan
                                </button>
                            </div>
                        </div>

                        <div class="w-full max-w-sm rounded-2xl border border-white/20 bg-white/15 p-4 text-white shadow-lg backdrop-blur">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-flag class="h-5 w-5 text-cyan-100" />
                                    <span>Semester</span>
                                </div>
                                <span class="font-semibold">{{ $semester->nama ?? 'N/A' }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-building-library class="h-5 w-5 text-cyan-100" />
                                    <span>Kelas</span>
                                </div>
                                <span class="font-semibold">{{ $kelas->nama ?? 'N/A' }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-shield-check class="h-5 w-5 text-cyan-100" />
                                    <span>Status</span>
                                </div>
                                <span class="rounded-full bg-emerald-400/20 px-2.5 py-1 text-xs font-semibold text-emerald-100">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Ringkasan (Summary Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 px-4 sm:px-0">
                <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-sky-900/40 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-sky-600 dark:text-sky-400">PBL Aktif</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active_pbl'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-2xl bg-sky-50 p-3 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300">
                            <x-heroicon-s-cube-transparent class="h-7 w-7" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-emerald-900/40 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">PBL Selesai</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['submissions'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <x-heroicon-s-check-badge class="h-7 w-7" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-amber-900/40 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">Kuis Belum Dikerjakan</p>
                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingQuizzesCount ?? 0 }}</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300">
                            <x-heroicon-s-question-mark-circle class="h-7 w-7" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-fuchsia-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-fuchsia-900/40 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-fuchsia-600 dark:text-fuchsia-400">Rata-rata Nilai</p>
                            @php $avg = $stats['average_grade'] ?? null; @endphp
                            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ is_numeric($avg) ? number_format($avg, 1) : 'N/A' }}</p>
                        </div>
                        <div class="rounded-2xl bg-fuchsia-50 p-3 text-fuchsia-600 dark:bg-fuchsia-900/30 dark:text-fuchsia-300">
                            <x-heroicon-s-academic-cap class="h-7 w-7" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-4 sm:px-0">

                <!-- Left Column: PBL Aktif & Mata Kuliah -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- 4. PBL Aktif (FITUR KEBARUAN) -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Problem Based Learning (PBL) Aktif</h2>
                        <div class="space-y-4">
                            @forelse ($tugasTerbaru as $pbl)
                                @php
                                    $deadline = \Carbon\Carbon::parse($pbl->deadline);
                                    $now = now();
                                    $isPast = $deadline->isPast();
                                    $diff = $now->diff($deadline);
                                    $daysRemaining = $isPast ? -$diff->days : $diff->days;
                                    $hoursRemaining = $diff->h;
                                    $urgencyClass = '';
                                    if ($isPast) {
                                        $urgencyClass = 'border-gray-300 dark:border-gray-600'; // Lewat
                                    } elseif ($daysRemaining <= 3) {
                                        $urgencyClass = 'border-red-500'; // Mendesak
                                    } elseif ($daysRemaining <= 7) {
                                        $urgencyClass = 'border-yellow-500'; // Segera
                                    } else {
                                        $urgencyClass = 'border-green-500'; // Aman
                                    }

                                    // Cek status submission. Pastikan relasi 'submissions' di-load di controller.
                                    $submission = $pbl->submissions->where('user_id', auth()->id())->first();
                                    $statusText = 'Pengerjaan';
                                    $statusClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200';
                                    if ($submission) {
                                        if ($submission->status === 'dinilai') {
                                            $statusText = 'Dinilai';
                                            $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                        } else {
                                            $statusText = 'Terkirim';
                                            $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                        }
                                    } elseif ($isPast) {
                                        $statusText = 'Terlewat';
                                        $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                    }
                                @endphp
                                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800 {{ $urgencyClass == 'border-gray-300 dark:border-gray-600' ? 'border-gray-200' : '' }}">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $pbl->judul }}</h3>
                                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusText }}</span>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {{ \Illuminate\Support\Str::limit($pbl->deskripsi, 120) }}
                                            </p>

                                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-700">
                                                    <x-heroicon-o-tag class="h-4 w-4" /> {{ $pbl->mataPelajaran->nama }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-700 {{ $daysRemaining <= 3 && !$isPast ? 'text-red-600 dark:text-red-400' : '' }}">
                                                    <x-heroicon-o-calendar-days class="h-4 w-4" />
                                                    @if($isPast)
                                                        Tenggat: {{ $deadline->isoFormat('D MMM YYYY') }} (Terlewat)
                                                    @elseif($daysRemaining === 0)
                                                        Sisa {{ $hoursRemaining }} jam
                                                    @else
                                                        Sisa {{ $daysRemaining }} hari {{ $hoursRemaining }} jam
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex flex-shrink-0 flex-wrap items-center gap-2 sm:flex-col sm:items-end">
                                            <a href="{{ route('mahasiswa.problems.show', $pbl) }}" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                                Lihat Tugas
                                            </a>
                                            <a href="{{ route('mahasiswa.problems.discussions.index', ['problem' => $pbl->id]) }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                Diskusi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                                    <x-heroicon-o-check-circle class="w-16 h-16 mx-auto text-green-500"/>
                                    <p class="mt-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Kerja bagus! Tidak ada PBL aktif saat ini.</p>
                                    <p class="text-gray-500 dark:text-gray-400">Semua tantangan telah kamu selesaikan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- 3. Mata Kuliah -->
                    

                <!-- Right Column: Notifikasi & Pengumuman -->
                <div class="lg:col-span-1 hidden md:block">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md sticky top-24">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-5">Notifikasi & Aktivitas</h2>
                        <div class="space-y-5 max-h-[600px] overflow-y-auto pr-2">
                            @forelse ($notifications ?? [] as $item)
                                <div class="flex items-start group">
                                    @if(!$item->is_read)
                                        <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2.5 -ml-3 mr-1"></div>
                                    @endif
                                    <div class="flex-shrink-0">
                                        @if($item->type == 'feedback')
                                            <div class="bg-green-100 dark:bg-green-900/50 p-2.5 rounded-full"><x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-green-500"/></div>
                                        @elseif($item->type == 'grade')
                                            <div class="bg-purple-100 dark:bg-purple-900/50 p-2.5 rounded-full"><x-heroicon-o-academic-cap class="w-5 h-5 text-purple-500"/></div>
                                        @elseif($item->type == 'pbl_update')
                                            <div class="bg-yellow-100 dark:bg-yellow-900/50 p-2.5 rounded-full"><x-heroicon-o-arrow-path class="w-5 h-5 text-yellow-500"/></div>
                                        @else {{-- announcement --}}
                                            <div class="bg-blue-100 dark:bg-blue-900/50 p-2.5 rounded-full"><x-heroicon-o-megaphone class="w-5 h-5 text-blue-500"/></div>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-grow">
                                        <p class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">{{ $item->title }}</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-1">{{ $item->content }}</p>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <x-heroicon-o-bell-slash class="w-12 h-12 mx-auto text-gray-400"/>
                                    <p class="mt-4 text-base font-semibold text-gray-600 dark:text-gray-300">Tidak ada notifikasi baru.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Panduan Modal -->
<style>
    .panduan-content { scrollbar-width: thin; scrollbar-color: #06b6d4 rgba(15,23,42,0.06); }
    .panduan-content::-webkit-scrollbar { width: 10px; }
    .panduan-content::-webkit-scrollbar-track { background: rgba(15,23,42,0.03); border-radius: 9999px; }
    .panduan-content::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#06b6d4,#34d399); border-radius: 9999px; box-shadow: inset 0 0 0 2px rgba(255,255,255,0.06); }
    .panduan-content::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg,#34d399,#06b6d4); }

    /* Page entrance transition */
    .page-enter { opacity: 0; transform: translateY(12px); transition: opacity 420ms ease-out, transform 420ms ease-out; }
    .page-enter.show { opacity: 1; transform: translateY(0); }
</style>

<div id="panduanModal" class="fixed inset-0 z-50 hidden items-start lg:items-center justify-center overflow-auto">
    <div class="absolute inset-0 bg-black/40 pointer-events-none" id="panduanBackdrop"></div>
    <div class="relative panduan-content pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-3xl w-full mx-4 p-6 z-10 max-h-[80vh] overflow-y-auto" style="-webkit-overflow-scrolling: touch; touch-action: auto;">
        <div class="flex items-start justify-between -mx-6 rounded-t-xl bg-gradient-to-r from-indigo-600 to-pink-500 p-4">
            <div class="px-6">
                <h3 class="text-xl font-bold text-white">Panduan Lengkap untuk Mahasiswa</h3>
                <p class="text-sm text-indigo-100 mt-1">Langkah singkat dan panduan menggunakan fitur untuk mahasiswa.</p>
            </div>
            <div class="px-6">
                <button id="closePanduan" class="text-white hover:text-indigo-200">✕</button>
            </div>
        </div>

        <div class="mt-4 text-gray-700 dark:text-gray-300 space-y-3 panduan-body px-6">
            <nav class="mb-4">
                <p class="font-semibold">Daftar Isi</p>
                <ul class="flex gap-3 flex-wrap text-xs mt-2">
                    <li><a href="#overview" class="text-indigo-600 hover:underline toc-link">Overview</a></li>
                    <li><a href="#persiapan" class="text-indigo-600 hover:underline toc-link">Persiapan</a></li>
                    <li><a href="#langkah" class="text-indigo-600 hover:underline toc-link">Langkah Pengerjaan</a></li>
                    <li><a href="#pengumpulan" class="text-indigo-600 hover:underline toc-link">Panduan Pengumpulan</a></li>
                    <li><a href="#penilaian" class="text-indigo-600 hover:underline toc-link">Penilaian</a></li>
                    <li><a href="#faq" class="text-indigo-600 hover:underline toc-link">FAQ</a></li>
                    <li><a href="#kontak" class="text-indigo-600 hover:underline toc-link">Kontak Bantuan</a></li>
                </ul>
            </nav>

            <section id="overview" class="mb-6">
                <h4 class="font-semibold">Overview</h4>
                <p class="mt-2">Halaman ini membantu mahasiswa mengelola dan menyelesaikan tugas Problem Based Learning (PBL). Gunakan fitur-fitur seperti <strong>Diskusi</strong>, <strong>Unggah Tugas</strong>, dan <strong>Notifikasi</strong> untuk mengikuti progress dan menerima umpan balik.</p>
            </section>

            <section id="persiapan" class="mb-6">
                <h4 class="font-semibold">Persiapan Sebelum Mengerjakan</h4>
                <ol class="list-decimal list-inside mt-2 space-y-2">
                    <li>Baca instruksi tugas secara keseluruhan dan pahami kriteria penilaian.</li>
                    <li>Siapkan bahan referensi (dokumen, gambar, atau link) yang diperlukan.</li>
                    <li>Pastikan koneksi internet stabil saat mengunggah file.</li>
                    <li>Siapkan file dalam format yang disarankan (PDF untuk dokumen, JPG/PNG untuk gambar).</li>
                </ol>
            </section>

            <section id="langkah" class="mb-6">
                <h4 class="font-semibold">Langkah-langkah Mengerjakan PBL</h4>
                <ol class="list-decimal list-inside mt-2 space-y-2">
                    <li>Buka tugas dengan menekan <strong>Lihat Tugas</strong> pada daftar PBL Aktif.</li>
                    <li>Pelajari deskripsi, lampiran, dan rubrik penilaian.</li>
                    <li>Jika ada kebingungan, tanyakan pada <strong>Diskusi</strong> atau dosen lewat komentar.</li>
                    <li>Selesaikan pekerjaan di perangkat lokal, lalu kumpulkan melalui tombol <strong>Unggah/Submit</strong>.</li>
                    <li>Setelah mengunggah, pastikan status berubah menjadi <em>Terkirim</em> atau sesuai pengumuman.</li>
                </ol>
            </section>

            <section id="pengumpulan" class="mb-6">
                <h4 class="font-semibold">Panduan Pengumpulan</h4>
                <ul class="list-disc list-inside mt-2 space-y-2">
                    <li>Klik tombol <strong>Unggah</strong> pada halaman tugas.</li>
                    <li>Pilih file dari perangkat — periksa kembali nama dan isi file sebelum submit.</li>
                    <li>Jika perlu mengunggah beberapa file, gabungkan ke ZIP atau gunakan fitur tambahan bila tersedia.</li>
                    <li>Perhatikan batas waktu. Setelah tenggat lewat, pengumpulan mungkin dinonaktifkan atau diberi status berbeda.</li>
                </ul>
            </section>

            <section id="penilaian" class="mb-6">
                <h4 class="font-semibold">Penilaian & Umpan Balik</h4>
                <p class="mt-2">Nilai dan umpan balik akan tampil di notifikasi dan pada halaman tugas. Waktu penilaian bergantung pada jumlah mahasiswa dan kebijakan pengajar.</p>
                <p class="mt-2">Jika nilai telah diberi, Anda dapat membuka detail umpan balik untuk melihat komentar dan perbaikan yang disarankan.</p>
            </section>

            <section id="faq" class="mb-6">
                <h4 class="font-semibold">FAQ (Pertanyaan Umum)</h4>
                <dl class="mt-2 space-y-2">
                    <dt class="font-medium">Apa format file yang disarankan?</dt>
                    <dd>PDF untuk dokumen, ZIP jika ada banyak file, JPG/PNG untuk gambar.</dd>
                    <dt class="font-medium">File saya terlalu besar, apa yang harus dilakukan?</dt>
                    <dd>Kompres file atau hubungi admin untuk opsi alternatif (mis. upload ke penyimpanan cloud dan kirim link).</dd>
                    <dt class="font-medium">Apakah saya bisa mengubah file setelah submit?</dt>
                    <dd>Tergantung pengaturan tugas; beberapa tugas mengizinkan revisi sebelum tenggat, sebagian lain tidak.</dd>
                </dl>
            </section>

            <section id="kontak" class="mb-2">
                <h4 class="font-semibold">Kontak Bantuan</h4>
                <p class="mt-2">Jika mengalami masalah teknis, hubungi administrator atau dosen. Sertakan detail masalah dan tangkapan layar bila perlu.</p>
            </section>
        </div>

        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('openPanduan');
    var modal = document.getElementById('panduanModal');
    var overlay = document.getElementById('panduanBackdrop');
    var close = document.getElementById('closePanduan');
    var close2 = document.getElementById('closePanduanFooter');
    var tocLinks = document.querySelectorAll('.toc-link');

    // Page entrance animation for dashboard
    var pageEnter = document.querySelector('.page-enter');
    if (pageEnter) {
        // small delay so initial render shows before transition
        setTimeout(function () { pageEnter.classList.add('show'); }, 40);
    }

    function openModal(){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); var body = modal.querySelector('.panduan-body'); if(body) body.scrollTop = 0; modal.scrollTop = 0; window.scrollTo(0,0); }}
    function closeModal(){ if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); }}

    if(btn) btn.addEventListener('click', openModal);
    // overlay set to pointer-events-none so we don't add overlay click handler
    if(close) close.addEventListener('click', closeModal);
    if(close2) close2.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    // Smooth scroll for internal TOC links inside modal
    tocLinks.forEach(function(link){
        link.addEventListener('click', function(e){
            e.preventDefault();
            var target = modal.querySelector(link.getAttribute('href'));
            if(target){
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>
