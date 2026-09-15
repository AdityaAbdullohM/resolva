<!-- Mobile off-canvas drawer (hidden by default) -->
<div id="mobileGuruDrawer" class="md:hidden hidden fixed inset-0 z-50">
    <div id="mobileGuruBackdrop" class="absolute inset-0 bg-black/40"></div>
    <div class="relative w-64 h-full bg-gradient-to-b from-sky-400 to-indigo-600 text-white p-4 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="font-bold text-lg">Resolva</div>
            <button id="closeMobileGuruDrawer" class="text-white text-2xl leading-none px-2 py-1">✕</button>
        </div>
        <nav>
            <ul>
                <li class="mb-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                        <x-heroicon-s-home class="h-5 w-5 mr-2 text-white/90" />
                        Dashboard Dosen
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('dosen.kelas.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.kelas.index') || request()->routeIs('guru.kelas.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                        <x-heroicon-s-building-library class="h-5 w-5 mr-2 text-white/90" />
                        Kelas Saya
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('dosen.materis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.materis.index') || request()->routeIs('guru.materis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                        <x-heroicon-s-book-open class="h-5 w-5 mr-2 text-white/90" />
                        Materi
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('dosen.kuis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.kuis.index') || request()->routeIs('guru.kuis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                        <x-heroicon-s-academic-cap class="h-5 w-5 mr-2 text-white/90" />
                        Kuis
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('dosen.problems.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.problems.*') || request()->routeIs('dosen.groups.*') || request()->routeIs('dosen.discussions.*') || request()->routeIs('dosen.penilaian.*') || request()->routeIs('guru.problems.*') || request()->routeIs('guru.groups.*') || request()->routeIs('guru.discussions.*') || request()->routeIs('guru.penilaian.*') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                        <x-heroicon-s-briefcase class="h-5 w-5 mr-2 text-white/90" />
                        Problem Based Learning
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
                    Dashboard Dosen
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('dosen.kelas.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.kelas.index') || request()->routeIs('guru.kelas.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-building-library class="h-5 w-5 mr-2 text-white/90" />
                    Kelas Saya
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('dosen.materis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.materis.index') || request()->routeIs('guru.materis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-book-open class="h-5 w-5 mr-2 text-white/90" />
                    Materi
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('dosen.kuis.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.kuis.index') || request()->routeIs('guru.kuis.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-academic-cap class="h-5 w-5 mr-2 text-white/90" />
                    Kuis
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('dosen.problems.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('dosen.problems.*') || request()->routeIs('dosen.groups.*') || request()->routeIs('dosen.discussions.*') || request()->routeIs('dosen.penilaian.*') || request()->routeIs('guru.problems.*') || request()->routeIs('guru.groups.*') || request()->routeIs('guru.discussions.*') || request()->routeIs('guru.penilaian.*') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-briefcase class="h-5 w-5 mr-2 text-white/90" />
                    Problem Based Learning
                </a>
            </li>
        </ul>
    </nav>
    <div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const drawer = document.getElementById('mobileGuruDrawer');
    const closeBtn = document.getElementById('closeMobileGuruDrawer');
    const backdrop = document.getElementById('mobileGuruBackdrop');

    window.addEventListener('toggle-mobile-sidebar', function () {
        if (!drawer) return;
        drawer.classList.toggle('hidden');
    });

    if (closeBtn) closeBtn.addEventListener('click', function () { drawer && drawer.classList.add('hidden'); });
    if (backdrop) backdrop.addEventListener('click', function () { drawer && drawer.classList.add('hidden'); });
});
</script>
