<x-app-layout>
   

<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Edit Kelompok: {{ $group->name }}</h1>
        <a href="{{ route('dosen.groups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('dosen.groups.update', $group->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Kelompok:</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" value="{{ old('name', $group->name) }}" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description">{{ old('description', $group->description) }}</textarea>
            </div>
            <div class="mb-4">
                <label for="problem_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Problem (opsional):</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="problem_id" name="problem_id">
                    <option value="">-- Umum / Tidak terkait problem --</option>
                    @foreach ($problems as $problem)
                        <option value="{{ $problem->id }}" {{ old('problem_id', $group->problem_id) == $problem->id ? 'selected' : '' }}>{{ $problem->judul }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="members" class="block text-gray-700 text-sm font-bold mb-2">Pilih Anggota (Siswa):</label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-40" id="members" name="members[]" multiple>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ in_array($student->id, old('members', $group->members->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $student->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-600 mt-1">Tahan CTRL atau Command untuk memilih beberapa anggota.</p>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Kelompok
                </button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>