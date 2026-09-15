<x-app-layout>
  

<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Detail Kelompok: {{ $group->name }}</h1>
        <a href="{{ route('dosen.groups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-4">
            <p class="text-gray-700 text-sm font-bold">Nama Kelompok:</p>
            <p class="text-gray-900">{{ $group->name }}</p>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 text-sm font-bold">Deskripsi:</p>
            <p class="text-gray-900">{{ $group->problem->deskripsi ?? 'N/A' }}</p>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 text-sm font-bold">Problem Terkait:</p>
            <p class="text-gray-900">{{ $group->problem->judul ?? 'N/A' }}</p>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 text-sm font-bold">Guru Pembuat:</p>
            <p class="text-gray-900">{{ $group->teacher->name ?? 'N/A' }}</p>
        </div>
        <div class="mb-4">
            <p class="text-gray-700 text-sm font-bold">Anggota Kelompok:</p>
            @if ($group->members->count() > 0)
                <ul class="list-disc list-inside">
                    @foreach ($group->members as $member)
                        <li>{{ $member->name }} ({{ $member->email }})</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-900">Tidak ada anggota dalam kelompok ini.</p>
            @endif
        </div>
        <div class="flex items-center justify-end">
            <a href="{{ route('dosen.groups.edit', $group->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">Ubah</a>
            <form action="{{ route('dosen.groups.destroy', $group->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this group?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Hapus</button>
            </form>
        </div>
    </div>
</div>
</x-app-layout>