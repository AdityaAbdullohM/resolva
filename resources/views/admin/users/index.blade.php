<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-200">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ __('Manajemen Pengguna') }}
                        </h2>
                        <div x-data="{ showImportModal: false }" class="flex items-center space-x-2">
                            <button type="button" @click="showImportModal = true" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded-md shadow-sm flex items-center transition-colors duration-200">
                                <x-heroicon-o-plus class="w-5 h-5 mr-2"/>
                                Impor
                            </button>

                            <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded-md shadow-sm flex items-center transition-colors duration-200">
                                <x-heroicon-o-plus class="w-5 h-5 mr-2"/>
                                Tambah Pengguna Baru
                            </a>

                            <!-- Import Modal -->
                            <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                                <div class="fixed inset-0 bg-black/50" @click="showImportModal = false"></div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 max-w-lg w-full p-6 mx-2">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Impor Pengguna (Excel)</h3>
                                        <button type="button" @click="showImportModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&times;</button>
                                    </div>
                                    <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih file (.xls, .xlsx, .csv)</label>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kolom yang didukung: name, email, nim/nis, role, password, kelas (nama atau id kelas).</p>
                                            <input type="file" name="file" accept=".xls,.xlsx,.csv" required class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700" />
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" @click="showImportModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">Batal</button>
                                            <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-400 text-white rounded-md">Impor</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4 flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-grow">
                            <input type="text" name="search" placeholder="Cari pengguna..." class="pl-10 pr-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full transition-colors duration-200" value="{{ request('search') }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <div class="relative">
                            <select name="role" class="block appearance-none w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 px-4 pr-8 rounded-md shadow-sm leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-700 focus:border-indigo-500 transition-colors duration-200">
                                <option value="" {{ request('role') == '' ? 'selected' : '' }}>Semua Peran</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Dosen</option>
                                <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Mahasiswa</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-md shadow-sm flex-shrink-0 transition-colors duration-200">
                            Filter
                        </button>
                    </form>

                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto rounded-lg shadow border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        No.
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Nama
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Peran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $index => $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $users->firstItem() + $index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                {{ $user->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ roleBadgeClass($user->role) }}">
                                                {{ roleDisplay($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 p-1 rounded-md bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition duration-150 ease-in-out flex items-center">
                                                    <x-heroicon-o-eye class="w-4 h-4 mr-1"/> Lihat
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 p-1 rounded-md bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 transition duration-150 ease-in-out flex items-center">
                                                    <x-heroicon-o-pencil class="w-4 h-4 mr-1"/> Edit
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block delete-user-form" data-name="{{ $user->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1 rounded-md bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 transition duration-150 ease-in-out flex items-center">
                                                        <x-heroicon-o-trash class="w-4 h-4 mr-1"/> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            Pengguna tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-user-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var name = form.dataset.name || 'pengguna';
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Apakah Anda yakin ingin menghapus ' + name + '?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush

</x-app-layout>