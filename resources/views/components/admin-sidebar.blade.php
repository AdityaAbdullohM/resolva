<aside class="siswa-sidebar w-64 bg-gradient-to-b from-sky-400 to-indigo-600 text-white p-4 min-h-screen flex flex-col justify-between shadow-lg rounded-tr-xl rounded-br-xl transition-all duration-300">
    <nav>
        <ul>
            <li class="mb-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-home class="h-5 w-5 mr-2 text-white/90" />
                    Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.users.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('admin.users.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-users class="h-5 w-5 mr-2 text-white/90" />
                    Pengguna
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.mata-kuliah.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('admin.mata-kuliah.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-book-open class="h-5 w-5 mr-2 text-white/90" />
                    Mata Kuliah
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.kelas.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('admin.kelas.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-building-library class="h-5 w-5 mr-2 text-white/90" />
                    Kelas
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center py-2 px-4 rounded-md transition-colors duration-200 {{ request()->routeIs('admin.tahun-ajaran.index') ? 'bg-white/20 text-white font-semibold' : 'text-white/90 hover:bg-white/10' }}">
                    <x-heroicon-s-calendar class="h-5 w-5 mr-2 text-white/90" />
                    Tahun Ajaran
                </a>
            </li>
        </ul>
    </nav>
    <div>
    </div>
</aside>
