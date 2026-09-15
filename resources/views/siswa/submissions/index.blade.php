<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Submission Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-6">Daftar Submission Anda</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            @if($submissions->isEmpty())
                <p>Anda belum mengumpulkan solusi untuk problem apa pun.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($submissions as $submission)
                        <li class="py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-semibold text-blue-600">Problem: {{ $submission->problem->judul }}</h2>
                                    <p class="text-sm text-gray-500">Kelas: {{ $submission->problem->kelas->nama }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Status: <span class="font-semibold">{{ ucfirst($submission->status) }}</span></p>
                                    @if($submission->nilai !== null)
                                        <p class="text-lg font-bold text-green-600">Nilai: {{ $submission->nilai }}</p>
                                    @else
                                        <p class="text-sm text-gray-500">Nilai: Belum dinilai</p>
                                    @endif
                                </div>
                            </div>
                            @if($submission->feedback)
                                <div class="mt-2 p-3 bg-gray-100 rounded text-sm text-gray-700">
                                    <p class="font-semibold">Feedback Dosen:</p>
                                    <p>{{ $submission->feedback }}</p>
                                </div>
                            @endif
                            @if($submission->file_path)
                                <p class="text-sm text-gray-600 mt-2">File:
                                    @if(is_array($submission->file_path))
                                        @foreach($submission->file_path as $i => $p)
                                            <a href="{{ route('submissions.file', $submission) }}?index={{ $i }}" target="_blank" class="text-blue-500 hover:text-blue-700">Unduh File {{ $i + 1 }}</a>@if(!$loop->last), @endif
                                        @endforeach
                                    @else
                                        <a href="{{ route('submissions.file', $submission) }}" target="_blank" class="text-blue-500 hover:text-blue-700">Unduh File</a>
                                    @endif
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</body>
</html>
