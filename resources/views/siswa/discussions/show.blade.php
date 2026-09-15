<x-app-layout>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ route('mahasiswa.discussions.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Forum Diskusi
                </a>
            </div>

            <!-- Main Discussion Topic -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight mb-2 sm:mb-0">
                            {{ $discussion->title }}
                        </h1>
                        @if($discussion->problem)
                        <a href="{{ route('mahasiswa.problems.show', $discussion->problem) }}" class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Tugas: {{ Str::limit($discussion->problem->judul, 25) }}
                        </a>
                        @endif
                    </div>

                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                        <img src="{{ $discussion->user->photo ? asset('storage/' . $discussion->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $discussion->user->name }}" class="h-8 w-8 rounded-full object-cover mr-3">
                        <p><span class="font-semibold text-gray-700 dark:text-gray-300">{{ $discussion->user->name }}</span> memulai diskusi {{ $discussion->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($discussion->content)) !!}
                    </div>
                </div>
            </div>

            <!-- New Reply Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <form action="{{ route('mahasiswa.discussions.posts.store', $discussion) }}" method="POST">
                        @csrf
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <img class="inline-block h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <label for="content" class="sr-only">Beri balasan</label>
                                <textarea id="content" name="content" rows="4" class="block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm placeholder-gray-500 dark:placeholder-gray-400" placeholder="Tulis balasan Anda..." required></textarea>
                                <div class="mt-3 flex items-center justify-end">
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900">
                                        Kirim Balasan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Discussion Posts (Replies) -->
            @if($discussion->posts->count() > 0)
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $discussion->posts->count() }} Balasan</h3>
                @foreach ($discussion->posts as $post)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-100 dark:border-gray-700">
                        <div class="p-5">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <img class="inline-block h-10 w-10 rounded-full object-cover" src="{{ $post->user->photo ? asset('storage/' . $post->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $post->user->name }}
                                            @if(in_array($post->user->role, ['guru', 'admin']))
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ roleBadgeClass($post->user->role) }}">
                                                    {{ roleDisplay($post->user->role) }}
                                                </span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="mt-2 prose prose-sm max-w-none text-gray-700 dark:text-gray-300">
                                        {!! nl2br(e($post->content)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Belum ada balasan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jadilah yang pertama membalas diskusi ini.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>