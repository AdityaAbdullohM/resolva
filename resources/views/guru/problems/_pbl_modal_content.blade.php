<div class="space-y-4">
    <div class="text-sm text-gray-600 mb-2">Tugas: <span class="font-semibold">{{ $problem->judul }}</span></div>

    @foreach($students as $student)
        <div class="border rounded p-3 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="font-medium">{{ $student->name }}</div>
                    <div class="text-xs text-gray-500">{{ $student->email ?? '' }}</div>
                </div>
                <div class="text-xs text-gray-500">Status: <span class="font-semibold">{{ $student->pbl_progress->validated_to ?? 0 }}/5</span></div>
            </div>

            <div class="grid grid-cols-5 gap-2">
                @for($step=1;$step<=5;$step++)
                    @php
                        $done = ($student->pbl_progress && ($student->pbl_progress->validated_to ?? 0) >= $step);
                        $pending = $student->pending_validations->firstWhere('step', $step);
                    @endphp
                    <div class="p-2 border rounded text-center bg-gray-50 dark:bg-gray-900">
                        <div class="text-xs font-semibold">{{ $step }}</div>
                        <div class="text-xs mt-1">
                            @if($done)
                                <span class="text-green-600">Selesai</span>
                            @elseif($pending)
                                <div class="text-yellow-600">Menunggu Validasi</div>
                                <div class="mt-2 flex gap-1 justify-center">
                                    <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                        <input type="hidden" name="step" value="{{ $step }}" />
                                        <input type="hidden" name="action" value="approve" />
                                        <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded">Setujui</button>
                                    </form>
                                    <form class="pbl-validate-form" data-problem-id="{{ $problem->id }}" action="{{ url('/dosen/problems/'.$problem->id.'/pbl-validate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $student->id }}" />
                                        <input type="hidden" name="step" value="{{ $step }}" />
                                        <input type="hidden" name="action" value="reject" />
                                        <button type="submit" class="px-2 py-1 text-xs bg-red-600 text-white rounded">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <div class="text-gray-500">Belum</div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endforeach
</div>
