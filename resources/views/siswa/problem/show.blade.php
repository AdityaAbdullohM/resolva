<x-app-layout>
   
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Detail Tugas</h3>
                        <a href="{{ route('mahasiswa.problems.discussions.create', ['problem' => $problem->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Mulai Diskusi Baru
                        </a>
                    </div>
                    <div class="mt-4 prose max-w-none">
                        {!! $problem->description !!}
                    </div>

                    <div class="mt-6">
                        <h4 class="font-semibold">Deadline:</h4>
                        <p>{{ $problem->deadline ? \Carbon\Carbon::parse($problem->deadline)->format('d F Y, H:i') : 'Tidak ada deadline' }}</p>
                    </div>

                    <div class="mt-6">
                        {{-- Submission form or status could go here --}}
                        <a href="{{ route('mahasiswa.problems.submit.create', $problem->id) }}" class="text-blue-500 hover:underline">Submit Tugas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>