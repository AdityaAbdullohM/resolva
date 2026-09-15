<x-app-layout>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header & Search -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Forum Diskusi</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Temukan jawaban, berbagi ide, dan diskusikan tugas bersama teman.</p>
                </div>
                <div class="w-full md:w-1/3">
                    <form action="{{ route('mahasiswa.discussions.index') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm shadow-sm transition duration-150 ease-in-out" 
                                placeholder="Cari topik diskusi...">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Discussion Cards Grid -->

            @if(request()->filled('group_id') && isset($isMember) && !$isMember)
                <div class="py-12">
                    <div class="max-w-3xl mx-auto text-center">
                        <p class="text-gray-600 dark:text-gray-400">Konten diskusi hanya dapat dilihat oleh anggota kelompok.</p>
                    </div>
                </div>
            @elseif(request()->filled('group_id') && isset($isMember) && $isMember)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Message feed -->
                        <div class="lg:col-span-2 bg-gradient-to-br from-white to-indigo-50 dark:from-gray-800 dark:to-indigo-900 rounded-2xl shadow-lg p-4 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('mahasiswa.kelompok.show', $group) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-100 shadow-sm hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    Kembali
                                </a>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $group->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Diskusi kelompok — riwayat pesan</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Anggota</span>
                                <div class="inline-flex -space-x-2">
                                    @foreach($group->members()->take(5)->get() as $m)
                                        <img class="h-8 w-8 rounded-full border-2 border-white dark:border-gray-800" src="{{ $m->photo ? asset('storage/' . $m->photo) : asset('images/default-avatar.png') }}" alt="{{ $m->name }}">
                                    @endforeach
                                    @if($group->members()->count() > 5)
                                        <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-semibold border-2 border-white dark:border-gray-800">+{{ $group->members()->count() - 5 }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div id="group-feed" class="space-y-4 max-h-[60vh] overflow-y-auto pr-3 pb-3">
                            @php
                                $posts = collect();
                                if (isset($groupDiscussion)) {
                                    try {
                                        $posts = $groupDiscussion->posts()->with('user')->orderBy('created_at','asc')->get();
                                    } catch (\Throwable $e) {
                                        $posts = collect();
                                    }
                                }
                            @endphp

                            @foreach($posts as $post)
                                @php $isMe = $post->user && $post->user->id === Auth::id(); @endphp
                                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[78%]">
                                        <div class="flex items-end {{ $isMe ? 'flex-row-reverse' : '' }}">
                                            <img class="h-9 w-9 rounded-full mr-3 ml-3" src="{{ $post->user && $post->user->photo ? asset('storage/' . $post->user->photo) : asset('images/default-avatar.png') }}" alt="{{ $post->user->name ?? 'User' }}">
                                            <div class="px-4 py-2 rounded-2xl {{ $isMe ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-100 dark:border-gray-700' }} shadow-sm">
                                                <div class="text-xs font-semibold mb-1">{{ $post->user->name ?? 'Pengguna' }} <span class="text-[10px] font-normal text-gray-400">• {{ $post->created_at->diffForHumans() }}</span></div>
                                                <div class="text-sm leading-relaxed">{!! nl2br(e($post->content)) !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                            <div class="mt-4">
                            <form action="{{ route('mahasiswa.discussions.store') }}?group_id={{ $group->id }}" method="POST">
                                @csrf
                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                <div class="flex items-center space-x-3">
                                    <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/default-avatar.png') }}" alt="Anda">
                                    <textarea name="content" rows="2" placeholder="Kirim pesan ke kelompok..." required class="flex-1 px-4 py-2 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"></textarea>

                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-pink-500 text-white rounded-full shadow-md">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow p-4 border border-gray-100 dark:border-gray-700">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tentang Kelompok</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ \Illuminate\Support\Str::limit($group->description, 180) }}</p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Anggota</h4>
                            <div class="mt-3 space-y-2">
                                @foreach($group->members()->take(8)->get() as $m)
                                    <div class="flex items-center space-x-3">
                                        <img class="h-8 w-8 rounded-full" src="{{ $m->photo ? asset('storage/' . $m->photo) : asset('images/default-avatar.png') }}" alt="{{ $m->name }}">
                                        <div class="text-sm text-gray-700 dark:text-gray-200">{{ $m->name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('mahasiswa.kelompok.show', $group) }}" class="block text-center px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-md">Lihat detail kelompok</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($discussions as $discussion)
                    <a href="{{ route('mahasiswa.discussions.show', $discussion) }}" class="group flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg dark:hover:shadow-gray-900/50 transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden h-full transform hover:-translate-y-1">
                        <div class="p-6 flex-grow">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $discussion->problem ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                                    {{ $discussion->problem ? 'Tugas' : 'Umum' }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $discussion->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 mb-3">
                                {{ $discussion->title }}
                            </h3>
                            
                            @if($discussion->problem)
                                <div class="flex items-start mb-4">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $discussion->problem->judul }}
                                    </p>
                                </div>
                            @endif

                            <div class="flex items-center mt-auto pt-4 border-t border-gray-50 dark:border-gray-700/50">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        {{ substr($discussion->user->name, 0, 2) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ $discussion->user->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-3 flex items-center justify-between group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 transition-colors duration-300 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span class="font-semibold">{{ $discussion->posts->count() }}</span>&nbsp;Balasan
                            </div>
                            <span class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold flex items-center opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                                Buka Diskusi <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                        </div>
                        @if(isset($isMember) && $isMember)
                            <div class="px-6 pb-6">
                                <form action="{{ route('mahasiswa.discussions.posts.store', $discussion) }}" method="POST">
                                    @csrf
                                    <div class="flex items-start space-x-3">
                                        <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/default-avatar.png') }}" alt="Anda">
                                        <div class="flex-1">
                                            <textarea name="content" rows="2" placeholder="Tulis balasan singkat..." class="block w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required></textarea>
                                            <div class="mt-2 text-right">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-md text-sm">Balas</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 text-center">
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-full p-4 mb-4">
                            <svg class="w-10 h-10 text-indigo-400 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belum ada diskusi</h3>
                        <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm">Jadilah yang pertama memulai diskusi di kelas ini! Tanyakan sesuatu atau bagikan ide Anda.</p>
                    </div>
                @endforelse
            </div>

            @endif

            <div class="mt-10">
                {{ $discussions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</x-app-layout>