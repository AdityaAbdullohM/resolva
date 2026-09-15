<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $discussion->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Discussion Topic -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">{{ $discussion->title }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            Terkait dengan tugas: <span class="font-semibold">{{ $discussion->problem->title }}</span>
                        </div>
                    </div>

                    <div class="mt-2 text-sm text-gray-500">
                        Dibuat oleh {{ $discussion->user->name }} - {{ $discussion->created_at->diffForHumans() }}
                    </div>

                    <div class="mt-6 prose max-w-none">
                        {!! $discussion->content !!}
                    </div>
                </div>
            </div>

            <!-- Discussion Posts (Replies) -->
            <div class="space-y-4">
                @forelse ($discussion->posts as $post)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex items-center mb-2">
                                <img src="{{ $post->user->photo ? asset('storage/' . $post->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}" class="h-8 w-8 rounded-full object-cover mr-3">
                                <div>
                                    <p class="font-semibold">{{ $post->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="prose max-w-none">
                                {!! $post->content !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500">
                        <p>Belum ada balasan.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.discussions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
