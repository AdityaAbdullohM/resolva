<x-app-layout>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ $discussion->problem ? route('dosen.problems.discussions.index', $discussion->problem) : route('dosen.discussions.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke {{ $discussion->problem ? 'Diskusi Tugas' : 'Forum Diskusi' }}
                </a>
            </div>

            <!-- Main Discussion Topic -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-4">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 leading-tight mb-2 sm:mb-0">
                            {{ $discussion->title }}
                        </h1>
                        @if($discussion->problem)
                        <a href="{{ route('dosen.problems.show', $discussion->problem) }}" class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Tugas: {{ Str::limit($discussion->problem->judul, 25) }}
                        </a>
                        @endif
                    </div>

                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <img src="{{ $discussion->user->photo ? asset('storage/' . $discussion->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $discussion->user->name }}" class="h-8 w-8 rounded-full object-cover mr-3">
                        <p><span class="font-semibold text-gray-700 dark:text-gray-300">{{ $discussion->user->name }}</span> ({{ ucfirst($discussion->user->role) }}) memulai diskusi {{ $discussion->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="px-6 pb-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="mt-6 prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($discussion->content)) !!}
                    </div>
                </div>
            </div>

            <!-- New Top-Level Reply Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                <form action="{{ route('dosen.discussions.posts.store', $discussion) }}" method="POST">
                        @csrf
                        {{-- No parent_id for top-level replies --}}
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <img class="inline-block h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="min-w-0 flex-1">
                                <label for="content" class="sr-only">Beri balasan</label>
                                <textarea id="content" name="content" rows="4" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Tulis balasan Anda..." required></textarea>
                                <div class="mt-3 flex items-center justify-end">
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Kirim Balasan
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
                </div>
            </div>

            <!-- Discussion Posts (Replies) -->
            @php
                // Filter for top-level posts only (where parent_id is null) and sort them
                $topLevelPosts = $discussion->posts->whereNull('parent_id')->sortBy('created_at');
            @endphp

            @if($topLevelPosts->count() > 0)
            <div class="space-y-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $discussion->posts->count() }} Balasan</h3>
                @foreach ($topLevelPosts as $post)
                    @include('guru.discussions._post', ['post' => $post, 'discussion' => $discussion])
                @endforeach
            </div>
            @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-gray-100">Belum ada balasan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jadilah yang pertama membalas diskusi ini.</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Ensure the function is defined only once to avoid issues with multiple includes
        if (typeof window.toggleReplyForm !== 'function') {
            window.toggleReplyForm = function(postId) {
                const form = document.getElementById('reply-form-' + postId);
                if (form) {
                    form.classList.toggle('hidden');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>