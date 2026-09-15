<x-app-layout>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <form action="{{ route('dosen.problems.store.general') }}" method="POST" enctype="multipart/form-data" x-data="problemForm()" @submit.prevent="submitForm">
                        @csrf
                        <div class="space-y-8">
                            <!-- Section 1: Class and Subject -->
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    <i class="fas fa-university mr-2"></i>
                                    Target Kelas & Mata Kuliah
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Class Selection -->
                                    <div>
                                        <label for="kelas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Kelas</label>
                                        <select name="kelas_id" id="kelas_id" x-model="kelasId" @change="fetchMataPelajaran" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition duration-150 ease-in-out" required>
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach ($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('kelas_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Mata Kuliah Selection -->
                                    <div>
                                        <label for="mata_pelajaran_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Mata Kuliah</label>
                                        <div class="relative">
                                            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition duration-150 ease-in-out" required :disabled="loading || mataPelajaran.length === 0">
                                                <option value="">-- Pilih Mata Kuliah --</option>
                                                <template x-for="mapel in mataPelajaran" :key="mapel.id">
                                                    <option :value="mapel.id" x-text="mapel.nama"></option>
                                                </template>
                                            </select>
                                            <div x-show="loading" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center rounded-md">
                                                <i class="fas fa-spinner fa-spin text-indigo-500 dark:text-indigo-400 text-xl"></i>
                                            </div>
                                        </div>
                                        @error('mata_pelajaran_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Section 4: Optional Attachments & Links -->
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    <i class="fas fa-paperclip mr-2"></i>
                                    Lampiran & Link (Opsional)
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah File (opsional)</label>
                                        <div class="mt-1 flex items-center gap-3">
                                            <input type="file" name="files[]" x-ref="files" @change="updateFiles" multiple class="block w-full text-sm text-gray-700 dark:text-gray-200">
                                        </div>
                                        <template x-if="files.length">
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <template x-for="(f, i) in files" :key="i">
                                                        <li x-text="f.name"></li>
                                                    </template>
                                                </ul>
                                                <button type="button" @click="clearFiles" class="mt-2 text-xs text-red-500 hover:underline">Hapus semua file</button>
                                            </div>
                                        </template>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link Tambahan (opsional)</label>
                                        <div class="mt-1 flex gap-2">
                                            <input type="url" x-model="newLink" placeholder="https://example.com" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100">
                                            <button type="button" @click="addLink" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">Tambah</button>
                                        </div>

                                        <template x-if="links.length">
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                <ul class="space-y-1">
                                                    <template x-for="(link, idx) in links" :key="idx">
                                                        <li class="flex items-center justify-between">
                                                            <a :href="link" target="_blank" class="text-indigo-700 dark:text-indigo-300 hover:underline truncate max-w-[70%]" x-text="link"></a>
                                                            <button type="button" @click="removeLink(idx)" class="text-xs text-red-500 hover:underline">Hapus</button>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        <!-- Hidden inputs to submit links[] to the server -->
                                        <template x-for="(link, idx) in links" :key="`hidden-link-`+idx">
                                            <input type="hidden" name="links[]" :value="link">
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Problem Details -->
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Detail Masalah
                                </h3>
                                <div class="space-y-6">
                                    <!-- Judul -->
                                    <div>
                                        <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Masalah</label>
                                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition duration-150 ease-in-out" required>
                                        @error('judul')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Deskripsi -->
                                    <div>
                                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Masalah</label>
                                        <div class="mt-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                                            <div class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 px-3 py-2 rounded-t-md">
                                                <button type="button" @click="tab = 'edit'" :class="{'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm': tab === 'edit', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'edit'}" class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-150">Edit</button>
                                                <button type="button" @click="tab = 'preview'" :class="{'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm': tab === 'preview', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': tab !== 'preview'}" class="px-3 py-1 text-sm font-medium rounded-md ml-2 transition-colors duration-150">Preview</button>
                                            </div>
                                            <div class="relative">
                                                <div x-show="tab === 'edit'" x-transition>
                                                    <textarea name="deskripsi" id="deskripsi" rows="12" class="block w-full border-0 rounded-b-md focus:ring-0 resize-vertical dark:bg-gray-800 dark:text-gray-100" x-model="deskripsi">{{ old('deskripsi') }}</textarea>
                                                </div>
                                                <div x-show="tab === 'preview'" x-transition class="p-4 prose dark:prose-invert max-w-none bg-white dark:bg-gray-800 rounded-b-md min-h-[288px] dark:text-gray-100" x-html="previewContent"></div>
                                            </div>
                                        </div>
                                        @error('deskripsi')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Metadata -->
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Metadata
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Kompetensi Java (Tag Input) -->
                                    <div>
                                        <label for="kompetensi_java_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan Pembelajaran</label>
                                        <div class="mt-1">
                                            <div class="flex flex-wrap gap-2 p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 shadow-sm">
                                                <template x-for="(tag, index) in tags" :key="index">
                                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                                        <span x-text="tag"></span>
                                                        <button @click="removeTag(index)" type="button" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                </template>
                                                <input type="text" id="kompetensi_java_input" x-model="newTag" @keydown.enter.prevent="addTag" @keydown.backspace="if (newTag === '') removeLastTag()" class="flex-grow border-0 focus:ring-0 p-0 dark:bg-gray-800 dark:text-gray-100" placeholder="...">
                                            </div>
                                            <input type="hidden" name="kompetensi_java" :value="tags.join(',')">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tekan Enter untuk menambah kompetensi.</p>
                                            @error('kompetensi_java')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Deadline -->
                                    <div>
                                        <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deadline </label>
                                        <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition duration-150 ease-in-out">
                                        @error('deadline')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <a href="{{ route('dosen.problems.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition duration-150 ease-in-out">Batal</a>
                            <button type="submit" class="ml-4 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.3/purify.min.js"></script>
    <script>
        function problemForm() {
            return {
                kelasId: '{{ old('kelas_id') }}',
                mataPelajaran: [],
                loading: false,
                tab: 'edit',
                deskripsi: `{!! old('deskripsi', '') !!}`,
                tags: {!! json_encode(old('kompetensi_java') ? array_map('trim', explode(',', old('kompetensi_java'))) : []) !!},
                newTag: '',
                // Attachments & links
                files: [],
                links: {!! json_encode(old('links', [])) !!},
                newLink: '',

                init() {
                    if (this.kelasId) {
                        this.fetchMataPelajaran().then(() => {
                            const oldMataPelajaranId = '{{ old('mata_pelajaran_id') }}';
                            if (oldMataPelajaranId) {
                                this.$nextTick(() => {
                                    document.getElementById('mata_pelajaran_id').value = oldMataPelajaranId;
                                });
                            }
                        });
                    }
                    
                    this.$watch('deskripsi', () => {
                        this.updatePreview();
                    });
                    this.updatePreview();
                },

                fetchMataPelajaran() {
                    if (!this.kelasId) {
                        this.mataPelajaran = [];
                        return Promise.resolve();
                    }

                    this.loading = true;
                    return fetch(`/dosen/get-mata-kuliah-by-kelas/${this.kelasId}`)
                        .then(response => response.json())
                        .then(data => {
                            this.mataPelajaran = data;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching Mata Kuliah:', error);
                            this.loading = false;
                            this.mataPelajaran = [];
                        });
                },

                previewContent: '',
                updatePreview() {
                    this.previewContent = DOMPurify.sanitize(marked.parse(this.deskripsi || 'Tidak ada konten untuk ditampilkan.'));
                },

                addTag() {
                    const tagToAdd = this.newTag.trim();
                    if (tagToAdd.length > 0 && !this.tags.includes(tagToAdd)) {
                        this.tags.push(tagToAdd);
                    }
                    this.newTag = '';
                },

                updateFiles(event) {
                    try{
                        const input = this.$refs.files;
                        this.files = Array.from(input.files || []);
                    }catch(e){ this.files = []; }
                },

                clearFiles() {
                    if(this.$refs.files){ this.$refs.files.value = null; }
                    this.files = [];
                },

                addLink() {
                    const l = (this.newLink || '').trim();
                    if(l && !this.links.includes(l)){
                        this.links.push(l);
                    }
                    this.newLink = '';
                },

                removeLink(index) { this.links.splice(index, 1); },

                removeTag(index) {
                    this.tags.splice(index, 1);
                },

                removeLastTag() {
                    if (this.newTag === '' && this.tags.length > 0) {
                        this.tags.pop();
                    }
                },

                submitForm(event) {
                    // Add any final client-side validation if needed
                    event.target.submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>