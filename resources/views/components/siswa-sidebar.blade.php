<!-- Mobile off-canvas drawer (hidden by default) -->
<div id="mobileSiswaDrawer" class="md:hidden hidden fixed inset-0 z-50">
    <div id="mobileSiswaBackdrop" class="absolute inset-0 bg-black/40"></div>
    <div class="relative w-64 h-full bg-gradient-to-b from-sky-400 to-indigo-600 text-white p-4 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="font-bold text-lg">Resolva</div>
            <button id="closeMobileSiswaDrawer" class="text-white text-2xl leading-none px-2 py-1">✕</button>
        </div>
        <nav>
            <ul>
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-home class="h-5 w-5 mr-2 text-white/90" />
                    Dashboard Mahasiswa
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.problems.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.problems.*') || request()->routeIs('siswa.kelompok.*') || request()->routeIs('siswa.discussions.*') || request()->routeIs('siswa.penilaian.*') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-briefcase class="h-5 w-5 mr-2 text-white/90" />
                    Problem Based Learning
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.quizzes.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.quizzes.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-academic-cap class="h-5 w-5 mr-2 text-white/90" />
                    Kuis
                </a>
            </li>
            
            <li class="mb-2">
                <a href="{{ route('mahasiswa.materis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.materis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-document-duplicate class="h-5 w-5 mr-2 text-white/90" />
                    Materi
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.compiler.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.compiler.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white/90" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.707 4.293a1 1 0 010 1.414L6.414 8h1.172a1 1 0 110 2H5a1 1 0 01-.707-1.707l3-3a1 1 0 011.414 0zM11.293 15.707a1 1 0 010-1.414L13.586 12H12.414a1 1 0 110-2H15a1 1 0 01.707 1.707l-3 3a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Compiler
                </a>
            </li>
        </ul>
    </nav>
    </div>
</div>

<!-- Desktop sidebar (hidden on small screens) -->
<aside class="siswa-sidebar hidden md:flex md:w-64 bg-gradient-to-b from-sky-400 to-indigo-600 text-white p-4 min-h-screen flex-col justify-between shadow-lg rounded-tr-xl rounded-br-xl transition-all duration-300">
    <nav>
        <ul>
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-home class="h-5 w-5 mr-2 text-white/90" />
                    Dashboard Mahasiswa
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.problems.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.problems.*') || request()->routeIs('siswa.kelompok.*') || request()->routeIs('siswa.discussions.*') || request()->routeIs('siswa.penilaian.*') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-briefcase class="h-5 w-5 mr-2 text-white/90" />
                    Problem Based Learning
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.quizzes.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.quizzes.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-academic-cap class="h-5 w-5 mr-2 text-white/90" />
                    Kuis
                </a>
            </li>
            
            <li class="mb-2">
                <a href="{{ route('mahasiswa.materis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.materis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-document-duplicate class="h-5 w-5 mr-2 text-white/90" />
                    Materi
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('mahasiswa.compiler.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('siswa.compiler.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white/90" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.707 4.293a1 1 0 010 1.414L6.414 8h1.172a1 1 0 110 2H5a1 1 0 01-.707-1.707l3-3a1 1 0 011.414 0zM11.293 15.707a1 1 0 010-1.414L13.586 12H12.414a1 1 0 110-2H15a1 1 0 01.707 1.707l-3 3a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Compiler
                </a>
            </li>
        </ul>
    </nav>
    <div>
      
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const drawer = document.getElementById('mobileSiswaDrawer');
    const closeBtn = document.getElementById('closeMobileSiswaDrawer');
    const backdrop = document.getElementById('mobileSiswaBackdrop');

    window.addEventListener('toggle-mobile-sidebar', function () {
        if (!drawer) return;
        drawer.classList.toggle('hidden');
    });

    if (closeBtn) closeBtn.addEventListener('click', function () { drawer && drawer.classList.add('hidden'); });
    if (backdrop) backdrop.addEventListener('click', function () { drawer && drawer.classList.add('hidden'); });
});
</script>