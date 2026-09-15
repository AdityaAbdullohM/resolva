<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PATCH')

                        @php
                            $isAdminCreator = auth()->check() && auth()->user()->role === 'admin';
                        @endphp

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password (optional) -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password Baru (opsional)')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Dosen</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Mahasiswa Kelas (conditionally displayed) -->
                        <div id="siswa-kelas-container" class="mt-4" style="display: {{ old('role', $user->role) == 'siswa' ? 'block' : 'none' }};">
                            <!-- NIS -->
                            <div>
                                <x-input-label for="nis" :value="__('NIM (Nomor Induk Mahasiswa)')" />
                                <x-text-input id="nis" class="block mt-1 w-full" type="text" name="nis" :value="old('nis', data_get($user, 'nis'))" autocomplete="off" />
                                <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                            </div>

                            <!-- Kelas -->
                            <!-- Kelas field removed: kelas not required on update -->
                        </div>

                        <!-- Dosen Fields (conditionally displayed) -->
                        <div id="guru-fields-container" class="mt-4" style="display: {{ old('role', $user->role) == 'guru' ? 'block' : 'none' }};">
                            <!-- NIP -->
                            <div class="mb-4">
                                <x-input-label for="nip" :value="__('NIP (Nomor Induk Pegawai)')" />
                                <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip', data_get($user, 'nip'))" autocomplete="off" />
                                <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                            </div>

                            <!-- Current Assignments -->
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-3">Tugas Mengajar Saat Ini</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <strong class="text-gray-600 dark:text-gray-400">Kelas yang Diajar:</strong>
                                        <ul class="list-disc list-inside mt-1">
                                            @forelse ($user->kelasYangDiajar ?? [] as $kelas)
                                                <li>{{ $kelas->nama }}</li>
                                            @empty
                                                <li class="list-none italic text-gray-500 dark:text-gray-400">Belum ada kelas yang diajar.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div>
                                        <strong class="text-gray-600 dark:text-gray-400">Mata Kuliah yang Diajar:</strong>
                                        @php
                                            $mataPelajaranDiajar = [];
                                            if (isset($user->kelasYangDiajar, $mataPelajarans)) {
                                                $mataPelajaranCollection = collect($mataPelajarans);
                                                $mataPelajaranMap = $mataPelajaranCollection->keyBy('id');
                                                foreach ($user->kelasYangDiajar as $kelasDiajar) {
                                                    $pivotId = $kelasDiajar->pivot?->mata_pelajaran_id ?? null;
                                                    if ($pivotId) {
                                                        $mp = $mataPelajaranMap->get($pivotId);
                                                        if (is_object($mp) || is_array($mp)) {
                                                            $mpId = data_get($mp, 'id');
                                                            $mpNama = data_get($mp, 'nama');
                                                            if ($mpId) {
                                                                $mataPelajaranDiajar[$mpId] = $mpNama;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <ul class="list-disc list-inside mt-1">
                                            @forelse ($mataPelajaranDiajar as $mp)
                                                <li>{{ $mp }}</li>
                                            @empty
                                                <li class="list-none italic text-gray-500 dark:text-gray-400">Belum ada Mata Kuliah yang diajar.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Informasi di atas adalah tugas mengajar yang sudah ada. Gunakan form di bawah untuk menambahkan tugas baru.</p>
                            </div>

                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-3 pt-4 border-t dark:border-gray-700">Tambah Tugas Mengajar Baru</h4>
                            <!-- Kelas yang Diajar -->
                            <div>
                                <x-input-label for="guru_kelas_id" :value="__('Kelas yang Diajar')" />
                                <select id="guru_kelas_id" name="guru_kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Kelas</option>
                                    @foreach(collect($kelas) as $k)
                                        @php
                                            $kId = data_get($k, 'id');
                                            $kNama = data_get($k, 'nama', $k);
                                        @endphp
                                        @if($kId)
                                            <option value="{{ $kId }}">{{ $kNama }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pilih kelas untuk ditambahkan. Dosen dapat mengajar lebih dari satu kelas. Anda bisa menambahkan kelas lain di halaman edit ini lagi setelah menyimpan.</p>
                                <x-input-error :messages="$errors->get('guru_kelas_id')" class="mt-2" />
                            </div>

                            <!-- Mata Kuliah yang Diajar removed as requested -->
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            
                            <x-primary-button class="ms-4">
                                                            {{ __('Update User') }}
                                                        </x-primary-button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-app-layout>
                            
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                        const roleSelect = document.getElementById('role');
                                        const siswaKelasContainer = document.getElementById('siswa-kelas-container');
                                        const guruFieldsContainer = document.getElementById('guru-fields-container');

                                        function toggleFields() {
                                            const val = roleSelect ? roleSelect.value : null;
                                            if (val === 'siswa') {
                                                if (siswaKelasContainer) siswaKelasContainer.style.display = 'block';
                                                if (guruFieldsContainer) guruFieldsContainer.style.display = 'none';
                                            } else if (val === 'guru') {
                                                if (siswaKelasContainer) siswaKelasContainer.style.display = 'none';
                                                if (guruFieldsContainer) guruFieldsContainer.style.display = 'block';
                                            } else {
                                                if (siswaKelasContainer) siswaKelasContainer.style.display = 'none';
                                                if (guruFieldsContainer) guruFieldsContainer.style.display = 'none';
                                            }
                                        }

                                        // Initial check
                                        toggleFields();

                                        // Listen for changes
                                        if (roleSelect) roleSelect.addEventListener('change', toggleFields);
                                });
                            </script>
                            @endpush