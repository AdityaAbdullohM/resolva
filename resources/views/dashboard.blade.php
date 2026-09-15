<x-app-layout>
    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    @if(Auth::user()->role === 'siswa')
                        <div class="mt-4">
                            <a href="{{ route('mahasiswa.problems.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Lihat Daftar Problem Anda</a>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('mahasiswa.submissions.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Lihat Daftar Submission Anda</a>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('mahasiswa.enroll.create') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Gabung Kelas Baru</a>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('mahasiswa.discussions.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Diskusi</a>
                        </div>
                    @endif

                    @if(Auth::user()->role === 'guru')
                        <div class="mt-4">
                            <a href="{{ route('dosen.kelas.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Kelola Kelas Anda</a>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('dosen.discussions.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Diskusi</a>
                        </div>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <div class="mt-4">
                            <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Kelola Pengguna (Admin)</a>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.discussions.index') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Diskusi</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
