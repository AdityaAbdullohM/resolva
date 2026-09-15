<x-app-layout>
  

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-200">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                        Tambah Pengguna
                    </h2>
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        @php
                            $isAdminCreator = auth()->check() && auth()->user()->role === 'admin';
                        @endphp

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Peran')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-colors duration-200" required>
                                <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Dosen</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- NIM Field (Mahasiswa) -->
                        <div id="nis-field" class="mt-4" style="display: {{ old('role') == 'siswa' ? 'block' : 'none' }};">
                            <x-input-label for="nis" :value="__('NIM (Nomor Induk Mahasiswa)')" />
                            <x-text-input id="nis" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="text" name="nis" :value="old('nis')" autocomplete="nis" />
                            <x-input-error :messages="$errors->get('nis')" class="mt-2" />
                        </div>

                        <!-- Kelas Field (Mahasiswa) - only show when creator is not admin -->
                        @unless($isAdminCreator)
                        <div id="kelas-siswa-field" class="mt-4" style="display: {{ old('role') == 'siswa' ? 'block' : 'none' }};">
                            <x-input-label for="kelas_id" :value="__('Kelas')" />
                            <select id="kelas_id" name="kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-colors duration-200">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ $k->jurusan }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kelas_id')" class="mt-2" />
                        </div>
                        @endunless

                        <!-- NIP Field (Dosen) -->
                        <div id="nip-field" class="mt-4" style="display: {{ old('role') == 'guru' ? 'block' : 'none' }};">
                            <x-input-label for="nip" :value="__('NIP (Nomor Induk Pegawai)')" />
                            <x-text-input id="nip" class="block mt-1 w-full dark:bg-gray-900 dark:text-white dark:border-gray-700" type="text" name="nip" :value="old('nip')" autocomplete="nip" />
                            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                        </div>

                        <!-- Kelas Field (Dosen) - only show when creator is not admin -->
                        @unless($isAdminCreator)
                        <div id="kelas-guru-field" class="mt-4" style="display: {{ old('role') == 'guru' ? 'block' : 'none' }};">
                            <x-input-label for="guru_kelas_id" :value="__('Kelas yang Diajar')" />
                            <select id="guru_kelas_id" name="guru_kelas_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm transition-colors duration-200">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('guru_kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ $k->jurusan }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('guru_kelas_id')" class="mt-2" />
                        </div>
                        @endunless

                        <!-- Mata Kuliah Field (Dosen) removed as requested -->

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const roleSelect = document.getElementById('role');
                const nisField = document.getElementById('nis-field');
                const kelasSiswaField = document.getElementById('kelas-siswa-field');
                const nipField = document.getElementById('nip-field');
                const kelasGuruField = document.getElementById('kelas-guru-field');
                const mapelGuruField = document.getElementById('mapel-guru-field');

                function toggleFields() {
                    const selectedRole = roleSelect ? roleSelect.value : null;
                    if (selectedRole === 'siswa') {
                        if (nisField) nisField.style.display = 'block';
                        if (kelasSiswaField) kelasSiswaField.style.display = 'block';
                        if (nipField) nipField.style.display = 'none';
                        if (kelasGuruField) kelasGuruField.style.display = 'none';
                        if (mapelGuruField) mapelGuruField.style.display = 'none';
                    } else if (selectedRole === 'guru') {
                        if (nisField) nisField.style.display = 'none';
                        if (kelasSiswaField) kelasSiswaField.style.display = 'none';
                        if (nipField) nipField.style.display = 'block';
                        if (kelasGuruField) kelasGuruField.style.display = 'block';
                        if (mapelGuruField) mapelGuruField.style.display = 'block';
                    } else {
                        if (nisField) nisField.style.display = 'none';
                        if (kelasSiswaField) kelasSiswaField.style.display = 'none';
                        if (nipField) nipField.style.display = 'none';
                        if (kelasGuruField) kelasGuruField.style.display = 'none';
                        if (mapelGuruField) mapelGuruField.style.display = 'none';
                    }
                }

                if (roleSelect) {
                    roleSelect.addEventListener('change', toggleFields);
                    // Initial call to set the correct visibility based on the old value or default
                    toggleFields();
                }
            });
        </script>
    @endpush
</x-app-layout>