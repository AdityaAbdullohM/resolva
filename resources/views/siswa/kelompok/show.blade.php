@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="relative">
                <div class="bg-gradient-to-r from-indigo-500 via-pink-500 to-teal-400 p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="text-white">
                            <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight">{{ $kelompok->name }}</h1>
                            <p class="mt-1 text-sm opacity-90">Kelas: {{ optional($kelompok->kelas)->nama ?? '-' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('mahasiswa.kelompok.index') }}" class="inline-flex items-center px-3 py-2 bg-white/90 text-indigo-700 rounded-lg shadow-sm hover:bg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Kembali
                            </a>
                            @if(isset($kelompok->problem))
                                <a href="{{ route('mahasiswa.problems.show', ['problem' => $kelompok->problem->id]) }}" class="inline-flex items-center px-3 py-2 bg-white/20 text-white border border-white/30 rounded-lg hover:bg-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M8.5 3a1 1 0 00-.894.553L5.382 7H3a1 1 0 000 2h2.382l2.224 3.447A1 1 0 008.5 13h7a1 1 0 000-2h-5.618l2.224-3.447A1 1 0 0012.5 5H9.118l.488-1.106A1 1 0 008.5 3z" />
                                    </svg>
                                    Tugas Terkait
                                </a>
                            @endif
                            <a href="{{ route('mahasiswa.discussions.index', ['group_id' => $kelompok->id]) }}" class="inline-flex items-center px-3 py-2 bg-white/90 text-indigo-700 rounded-lg shadow-sm hover:bg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v7a2 2 0 01-2 2H8l-4 3V5z" />
                                </svg>
                                Diskusi
                            </a>
                        </div>
                    </div>
                </div>
                <div class="-mt-6 px-6 sm:px-8">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Anggota Kelompok</h2>

                        @if($kelompok->members->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.25-1.264-.688-1.717M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.25-1.264.688-1.717m0 0A3.004 3.004 0 0112 15c1.22 0 2.313.737 2.812 1.823M12 12a3 3 0 100-6 3 3 0 000 6z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada anggota</h3>
                                <p class="mt-2 text-sm text-gray-500">Belum ada mahasiswa yang tergabung di kelompok ini.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($kelompok->members as $user)
                                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 rounded-lg shadow-sm flex items-center gap-3">
                                        <img class="h-12 w-12 rounded-full ring-2 ring-indigo-100" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                </div>
                                                <span class="text-xs font-medium text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">Anggota</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
