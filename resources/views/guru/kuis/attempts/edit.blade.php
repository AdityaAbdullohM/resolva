@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Ubah Attempt: {{ $attempt->user->name ?? 'Unknown' }}</h2>

        <form action="{{ route('dosen.kuis.attempts.update', $attempt) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Skor (%)</label>
                <input type="number" name="score" min="0" max="100" step="0.01" value="{{ old('score', $attempt->score) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                @error('score') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Mulai (Waktu)</label>
                <input type="datetime-local" name="start_time" step="1" value="{{ old('start_time', $attempt->start_time ? $attempt->start_time->format('Y-m-d\\TH:i:s') : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                @error('start_time') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Selesai (Waktu)</label>
                <input type="datetime-local" name="end_time" step="1" value="{{ old('end_time', $attempt->end_time ? $attempt->end_time->format('Y-m-d\\TH:i:s') : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                @error('end_time') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Status</label>
                <select name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                    <option value="started" @if(old('status', $attempt->status) == 'started') selected @endif>Started</option>
                    <option value="finished" @if(old('status', $attempt->status) == 'finished') selected @endif>Finished</option>
                    <option value="graded" @if(old('status', $attempt->status) == 'graded') selected @endif>Graded</option>
                </select>
                @error('status') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Simpan</button>
                <a href="{{ route('dosen.kuis.rekap', $attempt->quiz->id) }}" class="text-sm text-gray-600">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
