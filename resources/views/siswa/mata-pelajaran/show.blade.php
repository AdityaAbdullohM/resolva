<x-app-layout>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 1️⃣ Header Mata Kuliah -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $mataPelajaran->nama }}</h1>
                    <p class="text-lg text-gray-600">Dosen Pengampu: {{ $mataPelajaran->guru->first()->name ?? 'Belum ditentukan' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri -->
            <div class="lg:col-span-2 space-y-8">
                <!-- 2️⃣ Pengumuman Mata Kuliah -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">📌 Pengumuman</h2>
                    <div class="space-y-4">
                        @forelse ($mataPelajaran->pengumumans as $pengumuman)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <h3 class="font-semibold text-gray-800">{{ $pengumuman->judul }}</h3>
                                <p class="text-gray-600 text-sm">{{ $pengumuman->konten }}</p>
                                <span class="text-xs text-gray-400">Diposting: {{ $pengumuman->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada pengumuman untuk Mata Kuliah ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 4️⃣ Masalah / Tugas PBL -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">🚀 Masalah / Tugas PBL</h2>
                    <div class="space-y-4">
                        @forelse ($mataPelajaran->problems as $problem)
                            <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50 transition">
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-800">{{ $problem->judul }}</h3>
                                    <p class="text-gray-600 text-sm truncate max-w-md">{{ Str::limit($problem->deskripsi, 100) }}</p>
                                    <p class="text-sm text-red-500 font-semibold mt-1">Deadline: {{ \Carbon\Carbon::parse($problem->deadline)->format('d F Y, H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $submission = $problem->submissions->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if ($submission)
                                        @if ($problem->deadline && $submission->created_at > $problem->deadline)
                                            <span class="px-3 py-1 text-xs font-semibold text-white bg-red-500 rounded-full">Terlambat</span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">Sudah Dikumpulkan</span>
                                        @endif
                                    @else
                                         <span class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">Belum Dikerjakan</span>
                                    @endif
                                    <a href="{{ route('mahasiswa.problems.show', $problem) }}" class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada tugas untuk Mata Kuliah ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3️⃣ Materi Pembelajaran -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">📚 Materi Pembelajaran</h2>
                    <div class="space-y-3">
                         @forelse ($mataPelajaran->materis as $materi)
                            <a href="{{ route('mahasiswa.materi.show', ['kelas' => $materi->kelas_id, 'materi' => $materi->id]) }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        @if($materi->link_url)
                                            <x-heroicon-s-video-camera class="w-6 h-6 text-blue-500 mr-3"/>
                                        @else
                                            <x-heroicon-s-document-text class="w-6 h-6 text-blue-500 mr-3"/>
                                        @endif
                                        <span class="font-semibold text-gray-800">{{ $materi->judul }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-gray-500">Belum ada materi untuk Mata Kuliah ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="space-y-8">
                <!-- 7️⃣ Kelompok PBL -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">👥 Kelompok PBL</h2>
                     @php
                        $group = auth()->user()->groups()->whereHas('problem', function ($query) use ($mataPelajaran) {
                            $query->where('mata_pelajaran_id', $mataPelajaran->id);
                        })->first();
                    @endphp
                    @if($group)
                        <h3 class="font-semibold text-lg text-gray-800">{{ $group->nama }}</h3>
                        <ul class="mt-2 text-gray-600 text-sm space-y-1">
                            @foreach($group->members as $member)
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    {{ $member->name }} {{ $member->id == auth()->id() ? '(Anda)' : '' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Anda belum masuk ke dalam kelompok.</p>
                    @endif
                </div>

                <!-- 8️⃣ Diskusi Mata Kuliah -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">💬 Forum Diskusi</h2>
                    <p class="text-gray-600 mb-4">Diskusikan materi atau tugas dengan dosen dan teman sekelas.</p>
                    <a href="{{ route('mahasiswa.discussions.index') }}" class="w-full text-center bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                        Masuk ke Forum
                    </a>
                </div>

                <!-- 9️⃣ Nilai & Feedback -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">⭐ Nilai & Feedback</h2>
                    <div class="space-y-3">
                        <p class="text-gray-500 text-sm pt-2">Belum ada nilai yang dirilis.</p>
                    </div>
                     <a href="{{ route('mahasiswa.submissions.index') }}" class="mt-4 w-full text-center block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Lihat Semua Nilai
                    </a>
                </div>

                <!-- 🔟 Riwayat & Progres Belajar -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Progres Belajar</h2>
                    <div>
                        
                    </div>
                     <a href="#" class="mt-4 w-full text-center block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Lihat Riwayat Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>