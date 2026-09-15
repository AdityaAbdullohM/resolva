<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nilai & Umpan Balik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h1 class="text-2xl font-bold mb-8 text-center">Rekap Nilai dan Umpan Balik Tugas</h1>

                    @if ($submissions->isEmpty())
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Nilai</h3>
                            <p class="mt-1 text-sm text-gray-500">Anda belum memiliki nilai atau umpan balik untuk tugas yang dikumpulkan.</p>
                        </div>
                    @else
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Tugas
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Tanggal Pengumpulan
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Nilai
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Umpan Balik dari Dosen
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($submissions as $submission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                                        <a href="{{ route('mahasiswa.problems.show', $submission->problem) }}">
                                                            {{ $submission->problem->judul }}
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $submission->problem->kelas->nama }} - {{ $submission->problem->mataPelajaran->nama }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {{ $submission->submitted_at->isoFormat('D MMM YYYY, HH:mm') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if ($submission->nilai !== null)
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Dinilai
                                                        </span>
                                                    @else
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Belum Dinilai
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold {{ $submission->nilai !== null ? 'text-gray-900' : 'text-gray-500' }}">
                                                    {{ $submission->nilai ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-gray-600 {{ $submission->feedback ? '' : 'italic' }}">
                                                        {{ $submission->feedback ?? 'Belum ada umpan balik' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
