<x-app-layout>
    <div class="py-6 w-full px-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 w-full transition-colors duration-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white">Dashboard Admin</h3>
                    <div class="text-right">
                        <p class="text-lg text-gray-700 dark:text-gray-300">Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="current-day-date">--</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="current-time">--:--:--</p>
                        <div class="mt-3">
                            <button id="admin-guide-btn" type="button" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg shadow hover:bg-gray-800 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-8-4a1 1 0 00-.993.883L9 7v2a1 1 0 00.883.993L10 10h2a1 1 0 00.993-.883L13 9V7a1 1 0 00-1-1H10zM9 13a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 01-1 1H10a1 1 0 01-1-1v-1z" clip-rule="evenodd"/></svg>
                                Panduan Admin
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var btn = document.getElementById('admin-guide-btn');
                        var modal = document.getElementById('admin-guide-modal');
                        var overlay = document.getElementById('admin-guide-overlay');
                        var close = document.getElementById('admin-guide-close');
                        var close2 = document.getElementById('admin-guide-close-2');

                        function openModal(){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); }}
                        function closeModal(){ if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); }}

                        if(btn) btn.addEventListener('click', openModal);
                        // overlay set to pointer-events-none so we don't add overlay click handler
                        if(close) close.addEventListener('click', closeModal);
                        if(close2) close2.addEventListener('click', closeModal);
                    });
                </script>
                
                <!-- Admin Guide Modal -->
                <style>
                    /* Stylized scrollbar for admin guide modal */
                    .admin-guide-content { scrollbar-width: thin; scrollbar-color: #06b6d4 rgba(15,23,42,0.06); }
                    .admin-guide-content::-webkit-scrollbar { width: 10px; }
                    .admin-guide-content::-webkit-scrollbar-track { background: rgba(15,23,42,0.03); border-radius: 9999px; }
                    .admin-guide-content::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#06b6d4,#34d399); border-radius: 9999px; box-shadow: inset 0 0 0 2px rgba(255,255,255,0.06); }
                    .admin-guide-content::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg,#34d399,#06b6d4); }
                </style>

                <div id="admin-guide-modal" class="fixed inset-0 z-50 hidden items-start lg:items-center justify-center overflow-auto">
                    <div class="absolute inset-0 bg-black/40 pointer-events-none" id="admin-guide-overlay"></div>
                    <div class="relative admin-guide-content pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-3xl w-full mx-4 p-6 z-10 max-h-[80vh] overflow-y-auto" style="-webkit-overflow-scrolling: touch; touch-action: auto;">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Panduan Cepat: Admin Resolva</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Langkah singkat untuk mengelola platform.</p>
                            </div>
                            <button id="admin-guide-close" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">✕</button>
                        </div>

                        <div class="mt-4 text-gray-700 dark:text-gray-300 space-y-3">
                            <section>
                                <h5 class="font-semibold">1. Menambahkan Pengguna</h5>
                                <p class="text-sm">Langkah singkat untuk membuat akun baru:</p>
                                <ol class="list-decimal list-inside ml-4 text-sm space-y-1">
                                    <li>Buka halaman <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">Daftar Pengguna</a>.</li>
                                    <li>Klik tombol <strong>Tambah Pengguna Baru</strong>.</li>
                                    <li>Isi nama, email, peran (dosen/mahasiswa/admin) dan simpan.</li>
                                    <li>Jika ingin menetapkan foto, unggah file gambar berukuran minimal 256x256.</li>
                                </ol>
                                
                            </section>

                            <section>
                                <h5 class="font-semibold">2. Membuat dan Mengelola Kelas</h5>
                                <p class="text-sm">Untuk membuat kelas baru dan mengaitkannya ke guru dan Mata Kuliah:</p>
                                <ol class="list-decimal list-inside ml-4 text-sm space-y-1">
                                    <li>Buka <a href="{{ route('admin.kelas.index') }}" class="text-blue-600 hover:underline">Menu Kelas</a> dan klik <strong>Buat Kelas Baru</strong>.</li>
                                    <li>Pilih nama kelas, tahun ajaran, dan tambahkan dosen pengajar.</li>
                                    <li>Setelah kelas dibuat, buka detail kelas untuk menambahkan mahasiswa.</li>
                                </ol>
                                
                            </section>

                            <section>
                                <h5 class="font-semibold">3. Menambah Mata Kuliah</h5>
                                <p class="text-sm">Langkah untuk menambah Mata Kuliah dan menghubungkannya ke kelas:</p>
                                <ol class="list-decimal list-inside ml-4 text-sm space-y-1">
                                    <li>Buka <a href="{{ route('admin.mata-kuliah.index') }}" class="text-blue-600 hover:underline">Mata Kuliah</a>.</li>
                                    <li>Klik <strong>Tambah Mata Kuliah</strong>, isi nama dan deskripsi singkat.</li>
                                    <li>Gunakan halaman Kelas untuk mengaitkan Mata Kuliah ke kelas tertentu.</li>
                                </ol>
                            </section>

                            <section>
                                <h5 class="font-semibold">4. Mengatur Tahun Ajaran & Semester</h5>
                                <p class="text-sm">Pastikan hanya satu tahun ajaran dan semester yang diset sebagai aktif:</p>
                                <ol class="list-decimal list-inside ml-4 text-sm space-y-1">
                                    <li>Buka <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-blue-600 hover:underline">Tahun Ajaran</a> dan set status <em>aktif</em> pada yang sesuai.</li>
                                    <li>
                                        @if (Route::has('admin.semester.index'))
                                            Buka <a href="{{ route('admin.semester.index') }}" class="text-blue-600 hover:underline">Semester</a> untuk menandai semester aktif.
                                        @else
                                            Buka <span class="text-gray-600">menu Semester</span> untuk menandai semester aktif.
                                        @endif
                                    </li>
                                </ol>
                            </section>

                        

                            
                        </div>

                        <div class="mt-6 text-right">
                            <button id="admin-guide-close-2" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tutup</button>
                        </div>
                    </div>
                </div>


                <!-- System Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <a href="{{ route('admin.users.index', ['role' => 'siswa']) }}" class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg p-6 text-white flex items-center justify-between transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <div>
                            <div class="text-4xl font-bold">{{ $totalSiswa }}</div>
                            <div class="text-md opacity-90 mt-1">Total Mahasiswa</div>
                        </div>
                        <x-heroicon-o-users class="w-12 h-12 opacity-70" />
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'guru']) }}" class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl shadow-lg p-6 text-white flex items-center justify-between transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <div>
                            <div class="text-4xl font-bold">{{ $totalGuru }}</div>
                            <div class="text-md opacity-90 mt-1">Total Dosen</div>
                        </div>
                        <x-heroicon-o-user-group class="w-12 h-12 opacity-70" />
                    </a>
                    <a href="{{ route('admin.kelas.index') }}" class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl shadow-lg p-6 text-white flex items-center justify-between transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <div>
                            <div class="text-4xl font-bold">{{ $totalKelas }}</div>
                            <div class="text-md opacity-90 mt-1">Total Kelas</div>
                        </div>
                        <x-heroicon-o-academic-cap class="w-12 h-12 opacity-70" />
                    </a>
                    <a href="{{ route('admin.mata-kuliah.index') }}" class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl shadow-lg p-6 text-white flex items-center justify-between transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <div>
                            <div class="text-4xl font-bold">{{ $totalMataPelajaran }}</div>
                            <div class="text-md opacity-90 mt-1">Total Mata Kuliah</div>
                        </div>
                        <x-heroicon-o-book-open class="w-12 h-12 opacity-70" />
                    </a>
                </div>

                <script>
                    (function(){
                        function capitalize(s){ return s.charAt(0).toUpperCase() + s.slice(1); }
                        function updateDateTime(){
                            const now = new Date();
                            const day = now.toLocaleDateString('id-ID', { weekday: 'long' });
                            const date = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                            const time = now.toLocaleTimeString('id-ID');
                            const dayDate = capitalize(day) + ', ' + date;
                            const elDayDate = document.getElementById('current-day-date');
                            const elTime = document.getElementById('current-time');
                            if(elDayDate) elDayDate.textContent = dayDate;
                            if(elTime) elTime.textContent = time;
                        }
                        updateDateTime();
                        setInterval(updateDateTime, 1000);
                    })();
                </script>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Recent Users -->
                    <div class="lg:col-span-2">
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Pengguna Terbaru</h4>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-md transition-colors duration-200">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentUsers as $user)
                                    <li class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                        <div class="flex items-center">
                                            <img class="h-11 w-11 rounded-full object-cover" src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $user->name }}">
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ roleBadgeClass($user->role) }}">
                                            {{ roleDisplay($user->role) }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="p-6 text-center text-gray-500 dark:text-gray-400">Tidak ada pengguna baru.</li>
                                @endforelse
                            </ul>
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Lihat semua pengguna &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Info & Quick Actions -->
                    <div>
                        <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Informasi Akademik</h4>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-md p-6 mb-8 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <x-heroicon-o-calendar-days class="w-10 h-10 text-blue-500 dark:text-blue-400" />
                                <div>
                                    <p class="text-md text-gray-600 dark:text-gray-400">Tahun Ajaran Aktif</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $activeTahunAjaran ? $activeTahunAjaran->tahun : 'Belum diatur' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 mt-4">
                                <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-green-500 dark:text-green-400" />
                                <div>
                                    <p class="text-md text-gray-600 dark:text-gray-400">Semester Aktif</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $activeSemester ? $activeSemester->nama : 'Belum diatur' }}</p>
                                </div>
                            </div>
                             <div class="mt-5 text-right">
                                <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Kelola &rarr;</a>
                            </div>
                        </div>

                        <h4 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Aksi Cepat</h4>
                        <div class="space-y-4">
                            <a href="{{ route('admin.users.create') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all duration-200 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                                <x-heroicon-o-user-plus class="w-5 h-5 mr-3"/>
                                Tambah Pengguna Baru
                            </a>
                            <a href="{{ route('admin.kelas.create') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-all duration-200 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                                <x-heroicon-o-plus-circle class="w-5 h-5 mr-3"/>
                                Buat Kelas Baru
                            </a>
                             <a href="{{ route('admin.mata-kuliah.create') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-all duration-200 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                                <x-heroicon-o-book-open class="w-5 h-5 mr-3"/>
                                Tambah Mata Kuliah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>