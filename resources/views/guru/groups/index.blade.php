<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 py-8 lg:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-r-xl border-l-4 border-emerald-400 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 text-emerald-500">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-emerald-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6 rounded-2xl border border-white/70 bg-gradient-to-r from-indigo-600 via-violet-600 to-pink-500 p-5 text-white shadow-lg ring-1 ring-black/5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ route('dosen.problems.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/30" aria-label="Kembali ke Resolva">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali
                        </a>
                        <div>
                            <h2 class="text-2xl font-semibold">Kelompok</h2>
                            <p class="text-sm text-white/90">Kelola kelompok siswa dengan rapi, cepat, dan nyaman.</p>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center lg:w-auto">
                        <div class="relative w-full sm:w-72">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-white/80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" class="block w-full rounded-full border border-white/20 bg-white/20 py-2 pl-10 pr-3 text-sm text-white placeholder-white/80 outline-none ring-0 transition focus:bg-white/30" placeholder="Cari kelompok...">
                        </div>

                        <select id="filter-problem" class="w-full rounded-full border border-white/20 bg-white/20 px-4 py-2 text-sm text-white outline-none transition focus:bg-white/30 sm:w-48">
                            <option value="" class="text-gray-800">Semua Problem</option>
                            @foreach ($problems as $problem)
                                <option value="{{ $problem->id }}" class="text-gray-800">{{ $problem->judul }}</option>
                            @endforeach
                        </select>

                        <x-button-link-sm href="{{ route('dosen.groups.create') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Grup
                        </x-button-link-sm>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-white/20 pt-4 text-sm">
                    <span class="rounded-full bg-white/15 px-3 py-1">Total kelompok: <span class="font-semibold">{{ $groups->total() }}</span></span>
                    <span class="rounded-full bg-white/15 px-3 py-1">Total problem: <span class="font-semibold">{{ $problems->count() }}</span></span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($groups as $group)
                    <div class="group-card flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-xl" data-problem-id="{{ $group->problem_id }}">
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700">{{ strtoupper(substr($group->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-semibold text-gray-800">{{ $group->name }}</h3>
                                        <p class="mt-2 text-sm leading-6 text-gray-500">{{ $group->description ?: 'Tidak ada deskripsi.' }}</p>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-1.5 rounded-full bg-gray-50 px-2.5 py-1 text-sm text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                    <span>{{ $group->members->count() }}</span>
                                </div>
                            </div>

                            <div class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50/70 px-3 py-2">
                                <span class="inline-flex items-center gap-2 text-sm font-medium text-indigo-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                    </svg>
                                    {{ $group->problem->judul ?? 'Belum ada Problem' }}
                                </span>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <x-button-link-sm href="{{ route('dosen.groups.show', $group->id) }}" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-600 transition hover:bg-indigo-100" title="Lihat Detail">
                                    Lihat
                                </x-button-link-sm>
                                <x-button-link-sm href="{{ route('dosen.groups.edit', $group->id) }}" class="rounded-lg bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-600 transition hover:bg-amber-100" title="Edit">
                                    Edit
                                </x-button-link-sm>
                                <form action="{{ route('dosen.groups.destroy', $group->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelompok ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-100">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 xl:col-span-3">
                        <div class="rounded-2xl border border-gray-100 bg-white px-6 py-20 text-center shadow-sm">
                            <svg class="mx-auto h-20 w-20 text-pink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">Belum Ada Kelompok</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat kelompok baru untuk para siswa.</p>
                            <div class="mt-6">
                                <x-button-link href="{{ route('dosen.groups.create') }}" class="bg-indigo-600 text-white hover:bg-indigo-700">
                                    Buat Kelompok Pertama
                                </x-button-link>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 flex justify-center">
                {{ $groups->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('search');
                const filterProblem = document.getElementById('filter-problem');
                const groupCards = document.querySelectorAll('.group-card');

                function filterGroups() {
                    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                    const problemId = filterProblem ? filterProblem.value : '';

                    groupCards.forEach(card => {
                        const titleEl = card.querySelector('h3');
                        const groupName = titleEl ? titleEl.textContent.toLowerCase() : '';
                        const groupProblemId = card.dataset.problemId ? String(card.dataset.problemId) : '';

                        const matchesSearch = groupName.includes(searchTerm);
                        const matchesFilter = problemId === '' || groupProblemId === problemId;

                        if (matchesSearch && matchesFilter) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }

                if (searchInput) searchInput.addEventListener('input', filterGroups);
                if (filterProblem) filterProblem.addEventListener('change', filterGroups);
            });
        </script>
    @endpush
</x-app-layout>
