<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semua Diskusi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.discussions.index') }}" method="GET" class="mb-6">
                        <div class="flex items-center max-w-lg mx-auto">
                            <input type="text" name="search" placeholder="Cari diskusi..." value="{{ request('search') }}" class="form-input rounded-l-md shadow-sm w-full">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Cari
                            </button>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($discussions as $discussion)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                                <div class="p-6 flex-grow">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $discussion->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Terkait dengan tugas: <span class="font-semibold">{{ $discussion->problem->title }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Dibuat oleh {{ $discussion->user->name }} - {{ $discussion->created_at->diffForHumans() }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        <span class="font-semibold">{{ $discussion->posts->count() }}</span> balasan
                                    </p>
                                </div>
                                <div class="bg-gray-50 px-6 py-4">
                                    <a href="{{ route('admin.discussions.show', $discussion) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Lihat Diskusi &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500">
                                <p>Tidak ada diskusi saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $discussions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
