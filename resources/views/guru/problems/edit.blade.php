<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('dosen.problems.update', ['problem' => $problem->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Tugas</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul', $problem->judul) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('judul')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('deskripsi', $problem->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="kompetensi_java" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kompetensi Java</label>
                            <input type="text" name="kompetensi_java" id="kompetensi_java" value="{{ old('kompetensi_java', $problem->kompetensi_java) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g., Looping, Conditional, OOP">
                            @error('kompetensi_java')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deadline</label>
                            <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline', $problem->deadline ? \Carbon\Carbon::parse($problem->deadline)->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('deadline')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stored attachments & links (if any) --}}
                        @php
                            // Resolve possible stored files collection/array
                            $storedFiles = [];
                            if(isset($problem->files) && $problem->files) {
                                $storedFiles = $problem->files;
                            }

                            // Resolve possible stored links (could be JSON, CSV, array, or relation)
                            $storedLinks = [];
                            if(isset($problem->links) && $problem->links) {
                                $linksVal = $problem->links;
                                if(is_string($linksVal)){
                                    $decoded = json_decode($linksVal, true);
                                    if(is_array($decoded)){
                                        $storedLinks = $decoded;
                                    } else {
                                        $storedLinks = array_filter(array_map('trim', explode(',', $linksVal)));
                                    }
                                } elseif(is_array($linksVal) || $linksVal instanceof \Illuminate\Support\Collection) {
                                    $storedLinks = $linksVal;
                                }
                            }
                        @endphp

                            <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Lampiran & Link tersimpan</h4>

                                <div class="mb-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 font-medium mb-1">File:</div>
                                    @if(!empty($storedFiles))
                                        <ul class="list-disc pl-5 text-sm text-gray-700 dark:text-gray-200">
                                            @foreach($storedFiles as $f)
                                                @php
                                                    $filePath = $f->file_path ?? ($f['file_path'] ?? ($f->path ?? ($f->file ?? null)));
                                                    $originalName = $f->original_name ?? ($f['original_name'] ?? ($f->name ?? null));
                                                @endphp
                                                <li class="truncate">
                                                    @if($filePath)
                                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($filePath) }}" target="_blank" class="text-indigo-600 dark:text-indigo-300 hover:underline">{{ $originalName ?? basename($filePath) }}</a>
                                                    @else
                                                        <span>{{ $originalName ?? (is_string($f) ? $f : 'Tidak diketahui') }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Tidak ada file tersimpan.</div>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 font-medium mb-1">Link:</div>
                                    @if(!empty($storedLinks))
                                        <ul class="list-disc pl-5 text-sm text-gray-700 dark:text-gray-200">
                                            @foreach($storedLinks as $ln)
                                                <li class="truncate"><a href="{{ is_string($ln) ? $ln : (data_get($ln, 'url') ?? '#') }}" target="_blank" class="text-indigo-600 dark:text-indigo-300 hover:underline">{{ is_string($ln) ? $ln : (data_get($ln, 'title') ?? data_get($ln, 'url')) }}</a></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Tidak ada link tersimpan.</div>
                                    @endif
                                </div>
                            </div>
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                                Update Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>