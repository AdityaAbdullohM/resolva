@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('dosen.kelas.problems.submissions.index', [$kelas, $problem]) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Submissions
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-3">Nilai Submission</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">Siswa: <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $submission->user->name }}</span> • Tugas <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $problem->judul }}</span></p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white bg-indigo-600">{{ ucfirst($submission->status) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Code & Submission Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Pengumpulan</h2>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900">
                    @if($submission->content)
                        <div id="code-wrapper" class="relative overflow-auto rounded-lg">
                            <pre class="m-0"><code class="language-java hljs block p-6 text-sm min-h-[220px]">{{ $submission->content }}</code></pre>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-400 dark:text-gray-400">Tidak ada teks.</div>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-3 items-center">
                    @if($submission->file_path)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            @if(is_array($submission->file_path))
                                @foreach($submission->file_path as $i => $p)
                                    <a href="{{ route('submissions.file', $submission) }}?index={{ $i }}" target="_blank" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">Unduh File {{ $i + 1 }}</a>
                                @endforeach
                            @else
                                <a href="{{ route('submissions.file', $submission) }}" target="_blank" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">Unduh File</a>
                            @endif
                        </div>
                    @endif

                    <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->isoFormat('D MMM YYYY, HH:mm') }}</div>
                </div>
            </div>




        <!-- Right: Sidebar (grading + comment form) -->
        <aside class="space-y-6 sticky top-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-5">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Form Penilaian</h4>
                <form action="{{ route('dosen.kelas.problems.submissions.grade', [$kelas, $problem, $submission]) }}" method="POST">
                    @csrf @method('PATCH')
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nilai (0-100)</label>
                    <input type="number" name="nilai" id="nilai" value="{{ old('nilai', $submission->nilai) }}" min="0" max="100" required class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm py-2 px-3 mb-3 focus:ring-2 focus:ring-indigo-500">
                    @error('nilai') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror

                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Feedback (Opsional)</label>
                    <textarea name="feedback" id="feedback" rows="4" class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 dark:text-gray-100 rounded-lg shadow-sm py-2 px-3 mb-3 focus:ring-2 focus:ring-indigo-500">{{ old('feedback', $submission->feedback) }}</textarea>
                    @error('feedback') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full inline-flex justify-center items-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Simpan Penilaian</button>
                </form>
            </div>

        </aside>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.css">
<style>
    .hljs { background: transparent; }
    .hljs-ln-n { cursor: pointer; padding-right: 12px !important; color: #556b76; user-select: none; }
    .hljs-ln-n:hover { color: #2563eb; }
    .hljs-ln-code { padding-left: 8px !important; }
    /* Dark mode overrides for line numbers and code blocks */
    .dark .hljs { background: transparent; }
    .dark .hljs-ln-n { color: #9CA3AF; }
    .dark .hljs-ln-n:hover { color: #60a5fa; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/java.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    hljs.highlightAll();
    hljs.initLineNumbersOnLoad();

    const submissionId = {{ $submission->id }};
    const commentsContainer = document.getElementById('comments-container');
    const codeWrapper = document.getElementById('code-wrapper');
    const commentFormContainer = document.getElementById('comment-form-container');
    const commentLineNumberSpan = document.getElementById('comment-line-number');
    const newCommentTextarea = document.getElementById('new-comment-text');
    const saveCommentBtn = document.getElementById('save-comment-btn');
    const cancelCommentBtn = document.getElementById('cancel-comment-btn');
    const commentErrorMessage = document.getElementById('comment-error-message');
    const csrfToken = "{{ csrf_token() }}";

    let selectedLine = null;
    let editingCommentId = null;

    function showErrorMessage(message) {
        commentErrorMessage.textContent = message;
        commentErrorMessage.classList.remove('hidden');
    }
    function hideErrorMessage() { commentErrorMessage.classList.add('hidden'); commentErrorMessage.textContent = ''; }

    async function fetchAndDisplayComments() {
        commentsContainer.innerHTML = `<div class="text-center text-gray-400 p-4">Memuat komentar...</div>`;
        try {
            const res = await fetch(`/api/submissions/${submissionId}/comments`, { headers: { 'Accept':'application/json','X-CSRF-TOKEN': csrfToken } });
            if (!res.ok) throw new Error('Gagal memuat');
            const comments = await res.json();
            renderComments(comments);
        } catch (e) {
            commentsContainer.innerHTML = `<div class="text-center text-red-500 p-4">Gagal memuat komentar.</div>`;
            console.error(e);
        }
    }

    function renderComments(comments) {
        commentsContainer.innerHTML = '';
        if (!comments || comments.length === 0) {
            commentsContainer.innerHTML = `<div class="text-center text-gray-400 p-4">Belum ada komentar.</div>`;
            return;
        }
        comments.sort((a,b) => a.line_number - b.line_number);
        comments.forEach(c => {
            const el = document.createElement('div');
            el.className = 'p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100';
            el.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-sm font-semibold text-gray-800">${escapeHtml(c.user.name)}</div>
                        <div class="text-xs text-gray-500">Baris ${c.line_number}</div>
                    </div>
                    <div class="text-xs text-gray-400">${new Date(c.created_at).toLocaleString()}</div>
                </div>
                <div class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">${escapeHtml(c.comment)}</div>
                ${ c.can_edit ? `<div class="mt-2 flex gap-2"><button data-id="${c.id}" data-line="${c.line_number}" data-comment="${escapeHtml(c.comment)}" class="edit-comment text-indigo-600 text-sm">Edit</button><button data-id="${c.id}" class="delete-comment text-red-600 text-sm">Hapus</button></div>` : '' }
            `;
            commentsContainer.appendChild(el);
        });
    }

    function escapeHtml(s){ if(!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

    // Select line by clicking line number
    codeWrapper?.addEventListener('click', e => {
        const target = e.target;
        let ln = null;
        if (target.classList.contains('hljs-ln-n')) ln = target.dataset.lineNumber;
        else if (target.closest && target.closest('.hljs-ln-line')) {
            const n = target.closest('.hljs-ln-line').querySelector('.hljs-ln-n');
            if (n) ln = n.dataset.lineNumber;
        }
        if (ln) {
            selectedLine = parseInt(ln);
            commentLineNumberSpan.textContent = selectedLine;
            newCommentTextarea.value = '';
            editingCommentId = null;
            hideErrorMessage();
            newCommentTextarea.focus();
        }
    });

    // Delegation for edit/delete
    commentsContainer.addEventListener('click', async e => {
        const edit = e.target.closest('.edit-comment');
        const del = e.target.closest('.delete-comment');
        if (edit) {
            editingCommentId = edit.dataset.id;
            selectedLine = parseInt(edit.dataset.line);
            commentLineNumberSpan.textContent = selectedLine;
            newCommentTextarea.value = edit.dataset.comment;
            hideErrorMessage();
            newCommentTextarea.focus();
        } else if (del) {
            const id = del.dataset.id;
            if (!confirm('Hapus komentar ini?')) return;
            try {
                const res = await fetch(`/api/comments/${id}`, { method:'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken }});
                if (!res.ok) throw new Error('Gagal menghapus');
                fetchAndDisplayComments();
            } catch (err) { alert('Gagal menghapus komentar'); console.error(err); }
        }
    });

    saveCommentBtn.addEventListener('click', async () => {
        const text = newCommentTextarea.value.trim();
        if (!text || !selectedLine) { showErrorMessage('Pilih baris dan isi komentar.'); return; }
        try {
            const url = editingCommentId ? `/api/comments/${editingCommentId}` : `/api/submissions/${submissionId}/comments`;
            const method = editingCommentId ? 'PATCH' : 'POST';
            const res = await fetch(url, { method, headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ line_number: selectedLine, comment: text }) });
            if (!res.ok) {
                const err = await res.json().catch(()=>({ message: 'Gagal menyimpan' }));
                throw new Error(err.message || 'Gagal');
            }
            newCommentTextarea.value = '';
            editingCommentId = null;
            fetchAndDisplayComments();
            alert('Komentar tersimpan');
        } catch (err) { showErrorMessage(err.message); console.error(err); }
    });

    cancelCommentBtn.addEventListener('click', () => { selectedLine = null; editingCommentId = null; newCommentTextarea.value = ''; commentLineNumberSpan.textContent = '-'; hideErrorMessage(); });

    fetchAndDisplayComments();
});
</script>
@endpush