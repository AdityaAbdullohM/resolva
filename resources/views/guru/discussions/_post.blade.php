<div class="flex space-x-4">
    <div class="flex-shrink-0">
        <img class="inline-block h-10 w-10 rounded-full object-cover" src="{{ $post->user->photo ? asset('storage/' . $post->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}">
    </div>
    <div class="min-w-0 flex-1">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">
                    {{ $post->user->name }}
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ roleBadgeClass($post->user->role) }}">
                        {{ roleDisplay($post->user->role) }}
                    </span>
                </p>
                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
            </div>
            <div class="mt-2 prose prose-sm max-w-none text-gray-700">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>

        <div class="mt-2 flex items-center space-x-4">
            <button onclick="toggleReplyForm('{{ $post->id }}')" class="text-xs text-gray-500 hover:text-gray-800 font-semibold focus:outline-none">Balas</button>
        </div>

        <!-- Reply Form (hidden by default) -->
        <div id="reply-form-{{ $post->id }}" class="hidden mt-4">
            <form action="{{ route('dosen.discussions.posts.store', $discussion) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $post->id }}">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <img class="inline-block h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}">
                    </div>
                    <div class="min-w-0 flex-1">
                        <textarea name="content" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Tulis balasan untuk {{ $post->user->name }}..." required></textarea>
                        <div class="mt-2 flex items-center justify-end space-x-2">
                            <button type="button" onclick="toggleReplyForm('{{ $post->id }}')" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none">Batal</button>
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Kirim</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Nested Replies -->
        @if($post->replies->count() > 0)
            <div class="mt-6 space-y-6 pl-14">
                @foreach($post->replies->sortBy('created_at') as $reply)
                    @include('guru.discussions._post', ['post' => $reply, 'discussion' => $discussion])
                @endforeach
            </div>
        @endif
    </div>
</div>
