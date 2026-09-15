<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resolva - Platform Belajar Coding Modern untuk Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .glass-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        <header class="relative overflow-hidden bg-slate-950 text-white">
            <div class="absolute inset-0 hero-grid opacity-30"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.32),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(129,140,248,0.3),_transparent_30%)]"></div>

            <nav class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-indigo-500 font-bold shadow-lg shadow-cyan-500/20">
                        R
                    </div>
                    <div>
                        <p class="text-lg font-semibold tracking-tight">Resolva</p>
                        <p class="text-xs text-slate-300">Learning Platform</p>
                    </div>
                </a>

                <div class="hidden items-center gap-8 text-sm text-slate-300 md:flex">
                    <a href="#fitur" class="transition hover:text-white">Fitur</a>
                    <a href="#cara" class="transition hover:text-white">Cara Kerja</a>
                    <a href="#faq" class="transition hover:text-white">FAQ</a>
                </div>

                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                        Masuk
                    </a>
                @endauth
            </nav>

            <div class="relative z-10 mx-auto grid max-w-7xl gap-12 px-6 pb-20 pt-10 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:pb-24 lg:pt-16">
                <div>
                    <div class="inline-flex items-center rounded-full border border-cyan-400/25 bg-cyan-400/10 px-3 py-1 text-sm font-medium text-cyan-200">
                        <span class="mr-2 h-2.5 w-2.5 rounded-full bg-cyan-300"></span>
                        Platform Belajar Coding Modern
                    </div>
                    <h1 class="mt-6 text-4xl font-black tracking-tight text-white sm:text-5xl xl:text-7xl">
                        Bangun skill coding,
                        <span class="bg-gradient-to-r from-cyan-300 via-sky-300 to-indigo-300 bg-clip-text text-transparent">
                            siap kerja.
                        </span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg text-slate-300 sm:text-xl">
                        Resolva membantu mahasiswa belajar melalui project, problem-based learning, kuis, dan feedback langsung agar lebih siap menghadapi dunia industri.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-full bg-gradient-to-r from-cyan-400 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:-translate-y-1">
                                Masuk ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full bg-gradient-to-r from-cyan-400 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:-translate-y-1">
                                Mulai Sekarang
                            </a>
                        @endauth
                        <a href="#fitur" class="rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                            Jelajahi Fitur
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap gap-6 text-sm text-slate-300">
                        <div>
                            <p class="text-2xl font-bold text-white">100+</p>
                            <p>Latihan & tantangan</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">24/7</p>
                            <p>Akses materi & compiler</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">Realtime</p>
                            <p>Progress & feedback</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[28px] border border-white/15 p-5 shadow-2xl shadow-cyan-950/20">
                    <div class="rounded-[24px] bg-slate-900/70 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-cyan-300">Aktivitas Hari Ini</p>
                                <p class="mt-1 text-xl font-bold text-white">Progress belajar kamu</p>
                            </div>
                            <div class="rounded-full bg-emerald-500/15 px-3 py-1 text-sm font-semibold text-emerald-300">
                                Online
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-white">Problem Based Learning</p>
                                        <p class="text-sm text-slate-400">Tugas aktif sedang berjalan</p>
                                    </div>
                                    <p class="text-lg font-bold text-cyan-300">3</p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-white">Kuis belum dikerjakan</p>
                                        <p class="text-sm text-slate-400">Siap untuk mulai</p>
                                    </div>
                                    <p class="text-lg font-bold text-indigo-300">2</p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-white">Feedback terbaru</p>
                                        <p class="text-sm text-slate-400">Dari dosen dan mentor</p>
                                    </div>
                                    <p class="text-lg font-bold text-fuchsia-300">5</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section id="fitur" class="bg-white py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-600">Kenapa Resolva?</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Semua yang kamu butuhkan untuk belajar coding dengan lebih fokus</h2>
                    <p class="mt-4 text-lg text-slate-600">Resolva menggabungkan materi, praktik, serta evaluasi dalam satu platform yang nyaman dipakai setiap hari.</p>
                </div>

                <div class="mt-14 grid gap-8 md:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.75A2.25 2.25 0 1 0 12 11.25 2.25 2.25 0 0 0 12 6.75Zm0 8.25c-3.5 0-6.75 1.56-6.75 3.5V21h13.5v-2.5c0-1.94-3.25-3.5-6.75-3.5Z" /></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Kurikulum relevan industri</h3>
                        <p class="mt-3 text-slate-600">Materi disusun agar sesuai kebutuhan dunia kerja dan perkembangan teknologi saat ini.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 9h8m-8 4h5m-7 5h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" /></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Compiler & praktik langsung</h3>
                        <p class="mt-3 text-slate-600">Coba solusi kamu secara langsung dengan editor dan compiler yang siap dipakai di mana saja.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13.5 12 4l9 9.5M6.75 12v7.5h10.5V12" /></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Pantau progres belajar</h3>
                        <p class="mt-3 text-slate-600">Lihat progress, tugas, dan feedback secara terorganisir sehingga belajar terasa lebih terarah.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="cara" class="bg-slate-50 py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-600">Cara Kerja</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Belajar jadi lebih ringan dalam tiga langkah sederhana</h2>
                </div>

                <div class="mt-14 grid gap-8 lg:grid-cols-3">
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">1</div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Pilih materi atau tugas</h3>
                        <p class="mt-3 text-slate-600">Akses materi, PBL, dan kuis sesuai kelas dan topik yang sedang dipelajari.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">2</div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Kerjakan dan kumpulkan</h3>
                        <p class="mt-3 text-slate-600">Selesaikan tantangan, kirim jawaban, dan gunakan compiler untuk menguji hasil kerja kamu.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">3</div>
                        <h3 class="mt-6 text-xl font-semibold text-slate-900">Dapatkan feedback</h3>
                        <p class="mt-3 text-slate-600">Lihat hasil, nilai, dan feedback dari dosen agar bisa terus berkembang.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="bg-white py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="rounded-[32px] bg-gradient-to-r from-slate-900 via-indigo-950 to-cyan-900 p-8 text-white shadow-2xl sm:p-12">
                    <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Siap mulai?</p>
                            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Masuk dan rasakan pengalaman belajar yang lebih modern dan terarah.</h2>
                            <p class="mt-4 text-lg text-slate-300">Jadikan setiap sesi belajar lebih produktif dengan platform yang dirancang khusus untuk kebutuhan mahasiswa .</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Ke Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Masuk Sekarang</a>
                            @endauth
                            <a href="#" class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Hubungi Kami</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-slate-950 py-10 text-slate-400">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 text-center lg:flex-row lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-indigo-500 font-bold text-slate-950">
                        R
                    </div>
                    <span class="text-lg font-semibold text-white">Resolva</span>
                </div>
                <p>&copy; {{ date('Y') }} Resolva. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
