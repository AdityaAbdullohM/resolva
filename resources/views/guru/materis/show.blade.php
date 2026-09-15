<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ $materi->judul }}</h3>
                    <p class="mb-4">{{ $materi->deskripsi }}</p>

                    {{-- Display uploaded files --}}
                        @if ($materi->materiFiles->count() > 0)
                            @php $firstFile = $materi->materiFiles->first(); @endphp

                            {{-- Inline preview for first file (hidden on mobile to keep the page interactive in responsive mode) --}}
                            <div class="mb-6 hidden md:block">
{{-- Use the controller view route for inline preview so uploaded files are served through the app and path resolution works reliably --}}
                                <iframe id="inlinePreview" src="{{ route('dosen.kelas.materis.view', ['kelas' => $materi->kelas->id, 'materi' => $materi->id, 'materiFile' => $firstFile->id]) }}" class="w-full h-96 border dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900"></iframe>
                            </div>                                                       
                        @endif

                        <!-- Preview modal -->
                        <div id="previewModal" class="fixed inset-0 z-50 hidden">
                            <div class="absolute inset-0 bg-black opacity-50" onclick="closePreview()"></div>
                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <div class="bg-white dark:bg-gray-800 w-full max-w-4xl h-[80vh] rounded shadow-lg overflow-hidden relative">
                                    <div class="flex items-center justify-between p-3 border-b dark:border-gray-700">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">Preview File</h4>
                                        <button onclick="closePreview()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Tutup</button>
                                    </div>
                                    <iframe id="previewFrame" src="" class="w-full h-[calc(100%-56px)] bg-gray-50 dark:bg-gray-900" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                            function openPreview(url) {
                                const modal = document.getElementById('previewModal');
                                const frame = document.getElementById('previewFrame');
                                frame.src = url;
                                modal.classList.remove('hidden');
                            }

                            function closePreview() {
                                const modal = document.getElementById('previewModal');
                                const frame = document.getElementById('previewFrame');
                                frame.src = '';
                                modal.classList.add('hidden');
                            }

                            function setInlinePreview(url) {
                                const frame = document.getElementById('inlinePreview');
                                if (frame) {
                                    frame.src = url;
                                    frame.scrollIntoView({ behavior: 'smooth' });
                                }
                            }
                        </script>
                        @endpush

                    @if ($materi->link_url)
                        <div class="mb-4">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Link Materi:</p>
                            <a href="{{ $materi->link_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline transition">
                                {{ $materi->link_url }}
                            </a>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('dosen.materis.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
