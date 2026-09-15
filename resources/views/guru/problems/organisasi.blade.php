<x-app-layout>

    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <a href="{{ url()->previous() }}" class="inline-flex items-center text-indigo-200 hover:text-white mb-2">
                            &larr; Kembali
                        </a>
                        <h1 class="text-3xl font-extrabold tracking-tight">Organisasi: {{ $problem->judul }}</h1>
                        <p class="text-sm text-indigo-200 mt-1">Kelas: {{ $kelas->nama }} &middot; Mapel: {{ $problem->mataPelajaran->nama ?? 'Umum' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dosen.problems.show', $problem) }}" class="inline-flex items-center px-4 py-2 rounded-md bg-white/20 text-white text-sm font-semibold">Lihat Problem</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="-mt-10 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main column -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Manajemen Kelompok</h2>
                        <div class="mb-4">
                            <button id="btn-new-group" class="inline-flex items-center px-3 py-1 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Tambah Kelompok</button>
                        </div>

                        <div class="space-y-3">
                            @if(isset($groups) && $groups->isNotEmpty())
                                @foreach($groups as $g)
                                    <div class="border rounded p-3 bg-white dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="font-semibold">{{ $g->name ?? $g->nama ?? 'Kelompok #' . $g->id }}</div>
                                                <div class="text-xs text-gray-500">Anggota: {{ $g->members->count() }}</div>
                                                <div class="text-xs text-gray-400">{{ $g->description }}</div>
                                            </div>
                                            <div class="flex flex-col items-end gap-2">
                                                <a href="{{ route('dosen.groups.edit', $g) }}" class="px-2 py-1 bg-gray-100 dark:bg-gray-900 rounded text-xs">Edit</a>
                                                <button data-group-id="{{ $g->id }}" class="btn-manage-members px-2 py-1 bg-indigo-600 text-white rounded text-xs">Kelola Anggota</button>
                                                <form action="{{ route('dosen.groups.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini? Semua anggota akan dilepas.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm text-gray-500">Belum ada kelompok untuk tugas ini.</div>
                            @endif
                        </div>

                        <hr class="my-6" />
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Instruksi Tambahan</h2>
                        @php
                            $stage = 2;
                            $stageInstructions = data_get($problem->instructions, $stage, []);
                        @endphp
                        @if(!empty($stageInstructions) && is_array($stageInstructions))
                            <div class="prose dark:prose-invert text-gray-700 dark:text-gray-300 max-w-none">
                                <ul class="space-y-3">
                                    @foreach($stageInstructions as $idx => $inst)
                                        <li class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white font-semibold flex items-center justify-center">{{ $idx + 1 }}</div>
                                            <div class="text-sm">{!! nl2br(e(data_get($inst,'text') ?? $inst)) !!} <span class="text-xs text-gray-400">@if(!empty(data_get($inst,'created_at')))— {{ data_get($inst,'created_at') }}@endif</span></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-sm text-gray-600 dark:text-gray-300">Belum ada instruksi tambahan untuk tugas ini.</div>
                        @endif

                        <div class="mt-6">
                            <button id="add-instruction" data-problem-id="{{ $problem->id }}" data-stage="2" class="inline-flex items-center px-3 py-1 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Tambah Instruksi</button>
                        </div>

                        <!-- Students status for teacher (only for Sintaks 2 - Organisasi) -->
                        <div class="mt-6 border-t pt-5">
                            <h3 class="text-lg font-semibold mb-3">Status Siswa (Organisasi)</h3>
                            <div class="space-y-4">
                                @if(isset($students) && $students->isNotEmpty())
                                    @foreach($students as $student)
                                        <div class="border rounded p-4 bg-white dark:bg-gray-800">
                                            <div class="mb-2">
                                                <div class="font-medium">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $student->email ?? '' }}</div>
                                            </div>

                                            @if(!empty(optional($student->submission)->content))
                                                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded text-sm text-gray-700 dark:text-gray-300">
                                                    <div class="font-semibold text-xs text-gray-600 dark:text-gray-400 mb-1">Feedback / Jawaban Singkat</div>
                                                    <div class="whitespace-pre-line text-sm">{{ $student->submission->content }}</div>
                                                    @if(!empty($student->submission->submitted_at))
                                                        <div class="text-xs text-gray-500 mt-2">Dikirim: {{ \Carbon\Carbon::parse($student->submission->submitted_at)->diffForHumans() }}</div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Teacher feedback form (only for Sintaks 2 display) -->
                                            <div class="mt-3">
                                                <form class="teacher-feedback-form" data-problem-id="{{ $problem->id }}" action="{{ url('/dosen/problems/'.$problem->id.'/student-feedback') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Feedback Guru (Organisasi)</label>
                                                    <textarea name="feedback" rows="2" class="w-full rounded-md border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm p-2" placeholder="Tulis feedback singkat untuk siswa...">{{ optional($student->submission)->feedback }}</textarea>
                                                    <div class="mt-2 flex justify-end">
                                                        <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded">Simpan Feedback</button>
                                                    </div>
                                                </form>
                                            </div>

                                            @php
                                                $pendingList = collect($student->pending_validations ?? collect())->merge($student->pending_stage_completions ?? collect());
                                            @endphp

                                            @if($pendingList && $pendingList->isNotEmpty())
                                                <div class="mt-4">
                                                    @foreach($pendingList as $pending)
                                                        @php $step = data_get($pending, 'step') ?? data_get($pending, 'stage'); @endphp
                                                        @if($step == 2)
                                                            <div class="mb-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded flex items-center justify-between">
                                                                <div class="text-sm text-yellow-800 dark:text-yellow-200">Menunggu Validasi Langkah {{ $step }}</div>
                                                                <div class="flex gap-2">
                                                                    <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" data-student-name="{{ $student->name }}" data-step="{{ $step }}" data-action="approve" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                                        <input type="hidden" name="step" value="{{ $step }}" />
                                                                        <input type="hidden" name="action" value="approve" />
                                                                        <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded">Validasi</button>
                                                                    </form>
                                                                    <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" data-student-name="{{ $student->name }}" data-step="{{ $step }}" data-action="reject" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                                                        <input type="hidden" name="step" value="{{ $step }}" />
                                                                        <input type="hidden" name="action" value="reject" />
                                                                        <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded">Tidak Valid</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-sm text-gray-500">Tidak ada siswa terdaftar di kelas ini.</div>
                                @endif
                            </div>
                        </div>

                        
                        
                        
                    </div>

                </div>

                <!-- Sidebar -->
                <div>
                    <div class="space-y-4">
                        @php
                            $deadline = $problem->deadline ? \Carbon\Carbon::parse($problem->deadline) : null;
                            $isPastDeadline = $deadline && $deadline->isPast();
                        @endphp

                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase text-gray-500">Tenggat Waktu</p>
                                    @if($deadline)
                                        <p class="mt-2 text-lg font-bold {{ $isPastDeadline ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $deadline->isoFormat('D MMMM YYYY') }}</p>
                                        <p class="text-sm {{ $isPastDeadline ? 'text-red-500' : 'text-gray-600' }}">Pukul {{ $deadline->format('H:i') }} WIB</p>
                                    @else
                                        <p class="mt-2 text-sm text-green-600 font-bold">Tanpa Tenggat Waktu</p>
                                    @endif
                                </div>
                                <div class="text-indigo-500">
                                    <x-heroicon-s-calendar-days class="w-10 h-10" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-sm font-semibold {{ $isPastDeadline ? 'text-red-600' : 'text-indigo-600' }}">
                                    @if($isPastDeadline)
                                        Tenggat sudah lewat
                                    @else
                                        Sisa waktu {{ $deadline ? $deadline->diffForHumans(['parts' => 2, 'short' => true]) : '' }}
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-3">
                                <a href="{{ url('/dosen/problem-based-learning') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white/60 text-indigo-700 font-semibold border border-gray-200 hover:bg-white">Kembali ke Daftar</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('add-instruction');
            if(!btn) return;
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var id = this.dataset.problemId;
                var text = prompt('Masukkan instruksi baru untuk tugas ini:');
                if(!text || text.trim() === '') return;
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
                fetch("{{ url('/dosen/problems') }}/"+id+"/instructions", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ instruction: text, stage: 2 })
                }).then(function(res){ return res.json(); }).then(function(json){
                    if(json.success){
                        location.reload();
                    } else {
                        alert(json.message || 'Gagal menambah instruksi.');
                    }
                }).catch(function(){ alert('Gagal mengirim permintaan.'); });
            });
            
            // handle teacher feedback forms via AJAX
            document.querySelectorAll('.teacher-feedback-form').forEach(function(form){
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    var action = form.action;
                    var data = new FormData(form);
                    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch(action, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: data })
                        .then(function(res){ return res.json(); })
                        .then(function(json){
                            if(json.success){
                                location.reload();
                            } else {
                                alert(json.message || 'Gagal menyimpan feedback.');
                            }
                        }).catch(function(){ alert('Gagal mengirim permintaan.'); });
                });
            });
        });
    </script>

<script>
    (function(){
        function setupSweetConfirm(){
            var forms = document.querySelectorAll('.pbl-validate-form');
            forms.forEach(function(form){
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    var action = form.dataset.action || (form.querySelector('input[name="action"]') ? form.querySelector('input[name="action"]').value : '');
                    var student = form.dataset.studentName || '';
                    var step = form.dataset.step || '';
                    var title = action === 'approve' ? 'Konfirmasi Validasi' : (action === 'reject' ? 'Konfirmasi Penolakan' : 'Konfirmasi');
                    var text = action === 'approve' ? ('Yakin ingin menyetujui langkah ' + step + ' untuk siswa "' + student + '"?') : ('Yakin ingin menolak langkah ' + step + ' untuk siswa "' + student + '"?');

                    window.Swal.fire({
                        title: title,
                        text: text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then(function(result){
                        if(result.isConfirmed){
                            form.submit();
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function(){
            if(window.Swal) return setupSweetConfirm();
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            s.onload = setupSweetConfirm;
            document.head.appendChild(s);
        });
    })();
</script>

<!-- Modals for Group Management -->
<!-- Modal: New Group -->
<div id="modal-new-group" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-xl p-6">
        <h3 class="text-lg font-semibold mb-3">Buat Kelompok Baru</h3>
        <form id="form-new-group" action="{{ route('dosen.groups.store') }}" method="POST">
            @csrf
            <input type="hidden" name="problem_id" value="{{ $problem->id }}" />
            <div class="mb-3">
                <label class="block text-xs font-semibold mb-1">Nama Kelompok</label>
                <input name="name" class="w-full rounded border-gray-200 p-2" required />
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold mb-1">Deskripsi</label>
                <textarea name="description" class="w-full rounded border-gray-200 p-2"></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold mb-1">Anggota (pilih beberapa)</label>
                <select name="members[]" multiple class="w-full rounded border-gray-200 p-2 h-40">
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email ?? $s->nis ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="btn-cancel-new-group" class="px-3 py-1 bg-gray-200 rounded">Batal</button>
                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded">Buat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Manage Members -->
<div id="modal-manage-members" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-900 rounded-lg w-full max-w-xl p-6">
        <h3 id="manage-members-title" class="text-lg font-semibold mb-3">Kelola Anggota</h3>
        <form id="form-manage-members" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="block text-xs font-semibold mb-1">Pilih Anggota</label>
                <select id="manage-members-select" name="members[]" multiple class="w-full rounded border-gray-200 p-2 h-40">
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email ?? $s->nis ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="btn-cancel-manage-members" class="px-3 py-1 bg-gray-200 rounded">Batal</button>
                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        var btnNew = document.getElementById('btn-new-group');
        var modalNew = document.getElementById('modal-new-group');
        var btnCancelNew = document.getElementById('btn-cancel-new-group');
        if(btnNew){ btnNew.addEventListener('click', function(){ modalNew.classList.remove('hidden'); }); }
        if(btnCancelNew){ btnCancelNew.addEventListener('click', function(){ modalNew.classList.add('hidden'); }); }

        // Manage members
        var modalManage = document.getElementById('modal-manage-members');
        var manageTitle = document.getElementById('manage-members-title');
        var manageForm = document.getElementById('form-manage-members');
        var manageSelect = document.getElementById('manage-members-select');
        var btnCancelManage = document.getElementById('btn-cancel-manage-members');

        document.querySelectorAll('.btn-manage-members').forEach(function(btn){
            btn.addEventListener('click', function(){
                var gid = btn.dataset.groupId;
                var members = btn.dataset.groupMembers ? JSON.parse(btn.dataset.groupMembers) : [];
                var name = btn.dataset.groupName || ('Kelompok ' + gid);
                manageTitle.textContent = 'Kelola Anggota: ' + name;
                // set form action to /guru/groups/{id}
                manageForm.action = '{{ url('/dosen/groups') }}/' + gid;
                // clear selections
                for(var i=0;i<manageSelect.options.length;i++) manageSelect.options[i].selected = false;
                // select members
                members.forEach(function(id){
                    var opt = manageSelect.querySelector('option[value="'+id+'"]');
                    if(opt) opt.selected = true;
                });
                modalManage.classList.remove('hidden');
            });
        });

        if(btnCancelManage) btnCancelManage.addEventListener('click', function(){ modalManage.classList.add('hidden'); });
    });
</script>

@if(session('success') || session('error'))
    <script>
        (function(){
            function showMsg(){
                var type = {!! json_encode(session('success') ? 'success' : 'error') !!};
                var text = {!! json_encode(session('success') ?? session('error')) !!};
                if(window.Swal){
                    window.Swal.fire({title: type === 'success' ? 'Berhasil' : 'Gagal', text: text, icon: type});
                } else {
                    var s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                    s.onload = function(){ window.Swal.fire({title: type === 'success' ? 'Berhasil' : 'Gagal', text: text, icon: type}); };
                    document.head.appendChild(s);
                }
            }
            document.addEventListener('DOMContentLoaded', showMsg);
        })();
    </script>

@endif

</x-app-layout>
