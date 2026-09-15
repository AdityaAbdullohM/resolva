@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-gradient-to-b from-gray-50 to-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 bg-gradient-to-r from-indigo-500 via-pink-500 to-teal-400 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold">Daftar Kelompok</h1>
                <p class="mt-1 text-sm opacity-90">Pilih kelompok untuk tugas PBL atau bergabung dengan kelompok yang tersedia.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="search" name="q" placeholder="Cari kelompok..." class="pl-10 pr-4 py-2 rounded-full bg-white/20 placeholder-white text-white focus:outline-none focus:ring-2 focus:ring-white/30" />
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-white/90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                </div>
                <a href="{{ route('mahasiswa.kelompok.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-700 rounded-lg shadow">Segarkan</a>
                <button type="button" onclick="history.back()" class="inline-flex items-center px-3 py-2 bg-white/90 text-indigo-700 rounded-lg shadow">
                    Kembali
                </button>
            </div>
        </div>

        @if($groups->isEmpty())
            <div class="text-center py-20 px-6">
                <svg class="mx-auto h-12 w-12 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.25-1.264-.688-1.717M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.25-1.264.688-1.717m0 0A3.004 3.004 0 0112 15c1.22 0 2.313.737 2.812 1.823M12 12a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Kelompok</h3>
                <p class="mt-2 text-sm text-gray-500">Saat ini belum ada kelompok yang tersedia di kelas Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($groups as $group)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-200">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $group->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Kelas: {{ optional($group->kelas)->nama ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $group->members->count() }} anggota</div>
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-gray-600 min-h-[48px]">{{ Str::limit(optional($group->problem)->deskripsi ?? 'Tidak ada deskripsi.', 120) }}</p>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if(optional($group->problem)->judul)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-pink-50 text-pink-700">{{ optional($group->problem)->judul }}</span>
                                    @endif
                                </div>
                                <div>
                                    @if(!$group->members->contains(auth()->user()))
                                        <form action="{{ route('mahasiswa.kelompok.join', $group) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Gabung</button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-100 text-green-800 text-sm font-medium">Tergabung</span>
                                        <a href="{{ route('mahasiswa.kelompok.show', $group) }}" class="ml-2 text-sm font-semibold text-indigo-600">Lihat</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
