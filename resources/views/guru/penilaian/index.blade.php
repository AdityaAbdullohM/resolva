<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 px-4 sm:px-0">
                <h2 class="text-3xl font-bold text-gray-900">Riwayat Penilaian</h2>
                <p class="mt-1 text-sm text-gray-600">Lihat dan filter semua tugas yang telah dikumpulkan siswa.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6">
                    <!-- Filter and Search Form -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('dosen.penilaian.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <div class="col-span-1 md:col-span-2">
                                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Siswa/Tugas</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input type="text" name="search" id="search" placeholder="Ketik nama, email, atau judul tugas..."
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                               value="{{ $request->search ?? '' }}">
                                    </div>
                                </div>
                                <div>
                                    <label for="kelas_id" class="block text-sm font-medium text-gray-700">Filter Kelas</label>
                                    <select name="kelas_id" id="kelas_id"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="">Semua Kelas</option>
                                        @foreach ($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ ($request->kelas_id ?? '') == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="submit"
                                            class="w-full justify-center inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <x-heroicon-o-funnel class="w-4 h-4 mr-2"/>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if ($submissions->isEmpty())
                        <div class="text-center py-20 px-6 bg-gray-50 rounded-lg">
                            <x-heroicon-o-inbox-arrow-down class="mx-auto h-16 w-16 text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Submisi Ditemukan</h3>
                            <p class="mt-2 text-sm text-gray-500">Belum ada submisi tugas yang masuk atau sesuai dengan filter yang Anda terapkan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tugas</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Submit</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Aksi</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($submissions as $submission)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $submission->user->profile_photo_url }}" alt="{{ $submission->user->name }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $submission->user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $submission->user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900 min-w-[15rem]">
                                                    {{ $submission->problem->judul }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Kelas: {{ $submission->problem->kelas->nama }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClass = $submission->status == 'dinilai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                                    $statusIcon = $submission->status == 'dinilai' ? '<x-heroicon-s-check-circle class="w-4 h-4 mr-1.5"/>' : '<x-heroicon-s-clock class="w-4 h-4 mr-1.5"/>';
                                                @endphp
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }} items-center">
                                                    {!! $statusIcon !!}
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-xl font-bold {{ $submission->nilai ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ $submission->nilai ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('dosen.kelas.problems.submissions.show', ['kelas' => $submission->problem->kelas_id, 'problem' => $submission->problem_id, 'submission' => $submission->id]) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-indigo-100 border border-transparent rounded-lg font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-200 active:bg-indigo-300 focus:outline-none focus:border-indigo-300 focus:ring ring-indigo-200 disabled:opacity-25 transition ease-in-out duration-150">
                                                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2"/>
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8 px-6">
                            {{ $submissions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
