<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MateriFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use App\Models\MataPelajaran; // Added this import
use App\Models\Pertemuan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated guru.
     */
    public function index(Request $request)
    {
        $guruId = auth()->id();
        $kelasIds = auth()->user()->kelasYangDiajar()->pluck('kelas.id');

        Log::info('Guru ID: ' . $guruId);
        Log::info('Kelas IDs: ' . json_encode($kelasIds));

        $query = Materi::whereIn('kelas_id', $kelasIds)->with('kelas', 'mataPelajaran', 'materiFiles');

        // Handle search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%')
                    ->orWhereHas('kelas', function ($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('mataPelajaran', function ($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        // Normalize selected pertemuan value from query string.
        $selectedPertemuanNumber = null;
        $selectedPertemuanLabel = null;
        $pertemuanValue = $request->query('pertemuan');

        if ($pertemuanValue !== null) {
            if (is_numeric($pertemuanValue)) {
                $selectedPertemuanNumber = (int) $pertemuanValue;
            } elseif (preg_match('/(\d+)/', $pertemuanValue, $matches)) {
                $selectedPertemuanNumber = (int) $matches[1];
            }

            if ($selectedPertemuanNumber !== null) {
                $selectedPertemuanLabel = 'Pertemuan ' . $selectedPertemuanNumber;
                $query->where('pertemuan_number', $selectedPertemuanNumber);
            }
        }

        $materis = $query->latest()->paginate(12); // Paginate with 12 items per page
        Log::info('Materis count: ' . $materis->count());
        Log::info('Materis data: ' . json_encode($materis->toArray()));
        
        // Fetch pertemuan numbers from both Pertemuan table and Materi table for the authenticated guru's classes
        $pertemuanFromPertemuan = [];
        if (Schema::hasTable('pertemuans')) {
            $pertemuanFromPertemuan = Pertemuan::whereIn('kelas_id', $kelasIds)
                                        ->pluck('pertemuan_number')
                                        ->toArray();
        }

        $pertemuanFromMateri = Materi::whereIn('kelas_id', $kelasIds)
                                ->whereNotNull('pertemuan_number')
                                ->distinct()
                                ->pluck('pertemuan_number')
                                ->toArray();

        $pertemuanList = collect($pertemuanFromPertemuan)
                        ->merge($pertemuanFromMateri)
                        ->unique()
                        ->sort()
                        ->values()
                        ->toArray();

        $pertemuanCounts = Materi::whereIn('kelas_id', $kelasIds)
                    ->whereNotNull('pertemuan_number')
                    ->groupBy('pertemuan_number')
                    ->select('pertemuan_number', DB::raw('count(*) as cnt'))
                    ->pluck('cnt', 'pertemuan_number')
                    ->toArray();

        return view('guru.materis.index', compact('materis', 'pertemuanList', 'pertemuanCounts', 'selectedPertemuanLabel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kelas $kelas)
    {
        $mataPelajarans = $kelas->mataPelajaran;
        return view('guru.materis.create', compact('kelas', 'mataPelajarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:5120000', // Max 50MB per file
            'link_url' => 'nullable|url',
            'pertemuan_number' => 'nullable|integer|min:1',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
        ]);

        $materi = new Materi();
        $materi->judul = $request->judul;
        $materi->pertemuan_number = $request->pertemuan_number ?: null;
        $materi->deskripsi = $request->deskripsi;
        $materi->link_url = $request->link_url;
        $materi->kelas_id = $kelas->id;
        $materi->mata_pelajaran_id = $request->mata_pelajaran_id;
        $materi->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $sanitizedFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $sanitizedFileName . '.' . $extension;

                $path = $file->storeAs('materis/' . $kelas->id, $fileName, 'public');

                $materi->materiFiles()->create([
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        $flash = ['success' => 'Materi berhasil ditambahkan.'];
        if ($materi->pertemuan_number) {
            $flash['pertemuan_created'] = $materi->pertemuan_number;
            return redirect()->route('dosen.materis.index')->with($flash);
        }

        return redirect()->route('dosen.kelas.show', $kelas)->with($flash);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas, Materi $materi)
    {
        try {
            Log::info('show() called', [
                'kelas_id' => $kelas->id ?? null,
                'kelas_id_type' => gettype($kelas->id ?? null),
                'kelas_type' => get_class($kelas),
                'materi_id' => $materi->id ?? null,
                'materi_type' => get_class($materi),
                'materi.kelas_id' => $materi->kelas_id ?? null,
                'materi.kelas_id_type' => gettype($materi->kelas_id ?? null),
                'user_id' => auth()->id(),
            ]);

            $this->authorize('view', $materi);

            // Verify materi belongs to this kelas using numeric comparison
            if (! $this->isSameModelId($kelas->id, $materi->kelas_id)) {
                Log::error('Materi kelas mismatch', [
                    'kelas_id' => $kelasId,
                    'kelas_id_type' => gettype($kelasId),
                    'materi.kelas_id' => $materiKelasId,
                    'materi.kelas_id_type' => gettype($materiKelasId),
                    'materi_id' => $materi->id,
                ]);
                abort(404, 'Materi tidak ditemukan di kelas ini');
            }

            // Eager load materiFiles
            $materi->load('materiFiles');

            Log::info('show() success', [
                'kelas_id' => $kelas->id,
                'materi_id' => $materi->id,
                'files_count' => $materi->materiFiles->count(),
            ]);

            // Always display the materi detail page. The detail view contains
            // an explicit clickable link for `link_url` (opens in new tab),
            // so we avoid automatically redirecting away from the app when
            // a materi has an external link. This gives users a chance to
            // read the description and click the link intentionally.
            return view('guru.materis.show', compact('kelas', 'materi'));

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('Authorization exception in show()', [
                'kelas_id' => $kelas->id ?? 'unknown',
                'materi_id' => $materi->id ?? 'unknown',
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);
            abort(403, 'Anda tidak memiliki izin untuk melihat materi ini');
        } catch (\Exception $e) {
            Log::error('Exception in show()', [
                'kelas_id' => $kelas->id ?? 'unknown',
                'materi_id' => $materi->id ?? 'unknown',
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Display the specified resource without requiring kelas parameter.
     */
    public function showDirect(Materi $materi)
    {
        try {
            // Debug: Log the request
            Log::info('showDirect called', [
                'materi_id' => $materi->id ?? null,
                'user_id' => auth()->id(),
                'user_roles' => auth()->user() ? auth()->user()->roles()->pluck('name')->toArray() : [],
            ]);

            // Check if materi exists
            if (!$materi || !$materi->id) {
                Log::error('Materi not found or invalid', ['materi' => $materi]);
                abort(404, 'Materi tidak ditemukan');
            }

            // Try authorization
            try {
                $this->authorize('view', $materi);
            } catch (\Exception $authError) {
                Log::error('Authorization failed for showDirect', [
                    'materi_id' => $materi->id,
                    'user_id' => auth()->id(),
                    'error' => $authError->getMessage(),
                ]);
                throw $authError;
            }

            // Eager load materiFiles and kelas
            $materi->load('materiFiles', 'kelas');
            $kelas = $materi->kelas;

            if (!$kelas) {
                Log::error('Kelas not found for materi', ['materi_id' => $materi->id]);
                abort(404, 'Kelas tidak ditemukan untuk materi ini');
            }

            Log::info('showDirect success', [
                'materi_id' => $materi->id,
                'kelas_id' => $kelas->id,
                'materi_title' => $materi->judul,
            ]);

            // Always display the materi detail page. The detail view contains
            // an explicit clickable link for `link_url` (opens in new tab),
            // so we avoid automatically redirecting away from the app when
            // a materi has an external link. This gives users a chance to
            // read the description and click the link intentionally.
            return view('guru.materis.show', compact('kelas', 'materi'));

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('Authorization exception in showDirect', [
                'materi_id' => $materi->id ?? 'unknown',
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);
            abort(403, 'Anda tidak memiliki izin untuk melihat materi ini');
        } catch (\Exception $e) {
            Log::error('Exception in showDirect', [
                'materi_id' => $materi->id ?? 'unknown',
                'user_id' => auth()->id(),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Download the specified material file.
     */
    public function download(Kelas $kelas, Materi $materi, MateriFile $materiFile)
    {
        $this->authorize('view', $materi);

        if (! $this->fileBelongsToMateri($kelas, $materi, $materiFile)) {
            abort(404);
        }

        $resolved = $this->resolveMateriFilePath($materiFile);
        Log::info('MateriFile download request', [
            'kelas_id' => $kelas->id,
            'materi_id' => $materi->id,
            'materiFile_id' => $materiFile->id,
            'stored_path' => $materiFile->file_path,
            'resolved' => $resolved ? $resolved['absolute'] : null,
            'resolved_disk' => $resolved ? $resolved['disk'] : null,
        ]);

        if (! $resolved) {
            Log::warning('MateriFile download path resolution failed', [
                'materiFile_id' => $materiFile->id,
                'stored_path' => $materiFile->file_path,
            ]);
            abort(404, 'File materi tidak ditemukan.');
        }

        if ($resolved['disk']) {
            return Storage::disk($resolved['disk'])->download($resolved['path'], $materiFile->original_name);
        }

        return response()->download($resolved['absolute'], $materiFile->original_name);
    }

    /**
     * View the specified material file inline in browser.
     */
    public function viewFile(Kelas $kelas, Materi $materi, MateriFile $materiFile)
    {
        $this->authorize('view', $materi);

        if (! $this->fileBelongsToMateri($kelas, $materi, $materiFile)) {
            abort(404);
        }

        $resolved = $this->resolveMateriFilePath($materiFile);
        Log::info('MateriFile view request', [
            'kelas_id' => $kelas->id,
            'materi_id' => $materi->id,
            'materiFile_id' => $materiFile->id,
            'stored_path' => $materiFile->file_path,
            'resolved' => $resolved ? $resolved['absolute'] : null,
            'resolved_disk' => $resolved ? $resolved['disk'] : null,
        ]);

        if (! $resolved) {
            Log::warning('MateriFile view path resolution failed', [
                'materiFile_id' => $materiFile->id,
                'stored_path' => $materiFile->file_path,
            ]);
            // Fallback: try to find the file on the public disk using cleaned path
            $cleanPath = Str::startsWith($materiFile->file_path, 'public/') ? Str::after($materiFile->file_path, 'public/') : $materiFile->file_path;
            try {
                if (Storage::disk('public')->exists($cleanPath)) {
                    Log::info('MateriFile found on public disk via fallback', ['path' => $cleanPath]);
                    return Storage::disk('public')->response($cleanPath);
                }
            } catch (\Exception $e) {
                Log::warning('Fallback check on public disk failed: ' . $e->getMessage(), ['materiFile_id' => $materiFile->id]);
            }

            abort(404, 'File materi tidak ditemukan.');
        }

        if (! File::exists($resolved['absolute'])) {
            Log::warning('MateriFile resolved path missing on filesystem', [
                'materiFile_id' => $materiFile->id,
                'resolved_absolute' => $resolved['absolute'],
                'stored_path' => $materiFile->file_path,
            ]);

            // Another fallback: if resolved referenced a disk, try serving via Storage
            if (! empty($resolved['disk']) && in_array($resolved['disk'], ['public', 'local'])) {
                try {
                    if (Storage::disk($resolved['disk'])->exists($resolved['path'])) {
                        return Storage::disk($resolved['disk'])->response($resolved['path']);
                    }
                } catch (\Exception $e) {
                    Log::warning('Storage fallback failed: ' . $e->getMessage(), ['materiFile_id' => $materiFile->id]);
                }
            }

            abort(404, 'File materi tidak ditemukan.');
        }

        return response()->file($resolved['absolute']);
    }

    protected function isSameModelId($left, $right): bool
    {
        return (int) $left === (int) $right;
    }

    protected function fileBelongsToMateri(Kelas $kelas, Materi $materi, MateriFile $materiFile): bool
    {
        return $this->isSameModelId($kelas->id, $materi->kelas_id)
            && $this->isSameModelId($materi->id, $materiFile->materi_id);
    }

    protected function resolveMateriFilePath(MateriFile $materiFile)
    {
        $filePath = $materiFile->file_path;
        $cleanPath = Str::startsWith($filePath, 'public/') ? Str::after($filePath, 'public/') : $filePath;

        foreach (['local', 'public'] as $disk) {
            foreach ([$filePath, $cleanPath] as $path) {
                if (empty($path)) {
                    continue;
                }

                try {
                    if (Storage::disk($disk)->exists($path)) {
                        return [
                            'disk' => $disk,
                            'path' => $path,
                            'absolute' => Storage::disk($disk)->path($path),
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to resolve materiFile path on disk ' . $disk . ' for materiFile id ' . $materiFile->id . ': ' . $e->getMessage());
                }
            }
        }

        $pathsToTry = [
            storage_path('app/' . $filePath),
            storage_path('app/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/private/' . $filePath),
            storage_path('app/private/' . $cleanPath),
            storage_path('app/private/public/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path('storage/public/' . $cleanPath),
            public_path($filePath),
            public_path('storage/' . $filePath),
        ];

        foreach ($pathsToTry as $path) {
            if (File::exists($path)) {
                return [
                    'disk' => null,
                    'path' => $path,
                    'absolute' => $path,
                ];
            }
        }

        return null;
    }

    /**
     * Show the form for creating a new resource without a specific class.
     */
    public function createGeneral()
    {
        $user = auth()->user();
        $kelasList = $user->kelasYangDiajar()->get();
        $mataPelajarans = collect();

        foreach ($kelasList as $kelas) {
            $mataPelajarans = $mataPelajarans->merge($kelas->mataPelajaran);
        }

        $mataPelajarans = $mataPelajarans->unique('id');

        return view('guru.materis.create', compact('kelasList', 'mataPelajarans'));
    }

    /**
     * Store a newly created resource in storage from the general form.
     */
    public function storeGeneral(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:51200', // Max 50MB per file
            'link_url' => 'nullable|url',
            'pertemuan_number' => 'nullable|integer|min:1',
        ]);

        // Check if the authenticated user is actually the teacher for the selected class
        $kelas = Kelas::findOrFail($request->kelas_id);
        $isTeacherOfClass = $kelas->teachers()->where('user_id', auth()->id())->exists();

        if (!$isTeacherOfClass && auth()->user()->role !== 'admin') {
            // If the user is not an admin and not a teacher of that class, deny access.
            // This is a security check.
            abort(403, 'Anda tidak memiliki akses untuk menambahkan materi ke kelas ini.');
        }

        $materi = new Materi();
        $materi->judul = $request->judul;
        $materi->pertemuan_number = $request->pertemuan_number ?: null;
        $materi->deskripsi = $request->deskripsi;
        $materi->link_url = $request->link_url;
        $materi->kelas_id = $request->kelas_id;
        $materi->mata_pelajaran_id = $request->mata_pelajaran_id;
        $materi->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $sanitizedFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $sanitizedFileName . '.' . $extension;

                $path = $file->storeAs('materis/' . $request->kelas_id, $fileName, 'public');
                
                $materi->materiFiles()->create([
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        // Redirect to the general materials index
        $flash = ['success' => 'Materi berhasil ditambahkan.'];
        if ($materi->pertemuan_number) {
            $flash['pertemuan_created'] = $materi->pertemuan_number;
        }

        return redirect()->route('dosen.materis.index')->with($flash);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas, Materi $materi)
    {
        $this->authorize('update', $materi);
        return view('guru.materis.edit', compact('kelas', 'materi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas, Materi $materi)
    {
        $this->authorize('update', $materi);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:51200', // Max 50MB per file
            'link_url' => 'nullable|url',
            'deleted_files' => 'nullable|array', // To handle deletion of existing files
            'deleted_files.*' => [
                'nullable',
                'integer',
                Rule::exists('materi_files', 'id')->where(function ($query) use ($materi) {
                    $query->where('materi_id', $materi->id);
                }),
            ],
            'pertemuan_number' => 'nullable|integer|min:1',
        ]);

        $materi->judul = $request->judul;
        $materi->pertemuan_number = $request->pertemuan_number ?: null;
        $materi->deskripsi = $request->deskripsi;
        $materi->link_url = $request->link_url;
        $materi->save();

        // Handle file deletions
        if ($request->has('deleted_files')) {
            $deletedFileIds = array_filter($request->input('deleted_files', []), fn ($id) => is_numeric($id));
            $filesToDelete = $materi->materiFiles()->whereIn('id', $deletedFileIds)->get();

            foreach ($filesToDelete as $materiFile) {
                $resolved = $this->resolveMateriFilePath($materiFile);
                if ($resolved) {
                    if ($resolved['disk']) {
                        Storage::disk($resolved['disk'])->delete($resolved['path']);
                    } else {
                        File::delete($resolved['absolute']);
                    }
                } else {
                    if (!empty($materiFile->file_path)) {
                        Storage::disk('public')->delete($materiFile->file_path);
                        Storage::delete($materiFile->file_path);
                    }
                }

                $materiFile->delete();
            }
        }

        // Handle new file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $sanitizedFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $sanitizedFileName . '.' . $extension;

                $path = $file->storeAs('materis/' . $kelas->id, $fileName, 'public');
                
                $materi->materiFiles()->create([
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        return redirect()->route('dosen.materis.index')->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas, Materi $materi)
    {
        $this->authorize('delete', $materi);

        // Delete associated files
        foreach ($materi->materiFiles as $file) {
            Storage::delete($file->file_path);
            $file->delete();
        }
        $materi->delete();

        return redirect()->route('dosen.materis.index')->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Remove pertemuan assignment for a given pertemuan number (non-destructive).
     * Sets `pertemuan_number` to null for all materi in teacher's classes matching the number.
     */
    public function destroyPertemuan(Request $request, $number)
    {
        $guruId = auth()->id();
        $kelasIds = auth()->user()->kelasYangDiajar()->pluck('kelas.id')->toArray();

        if (!is_numeric($number)) {
            return redirect()->back()->with('error', 'Nomor pertemuan tidak valid.');
        }

        $num = (int) $number;

        // Find matching materi owned by this teacher's classes
        $materis = Materi::whereIn('kelas_id', $kelasIds)
                    ->where('pertemuan_number', $num)
                    ->get();

        if ($materis->isEmpty()) {
            return redirect()->route('dosen.materis.index')->with('info', 'Tidak ada materi yang menggunakan pertemuan ini.');
        }

        $deletedCount = 0;
        foreach ($materis as $materi) {
            // delete associated files from storage
            foreach ($materi->materiFiles as $file) {
                try {
                    $resolved = $this->resolveMateriFilePath($file);
                    if ($resolved) {
                        if ($resolved['disk']) {
                            Storage::disk($resolved['disk'])->delete($resolved['path']);
                        } else {
                            File::delete($resolved['absolute']);
                        }
                    }
                } catch (\Exception $e) {
                    // continue even if a file delete fails
                    Log::warning('Gagal menghapus file materi: ' . $file->file_path . ' - ' . $e->getMessage());
                }
                $file->delete();
            }

            // delete the materi record
            $materi->delete();
            $deletedCount++;
        }

        // Also delete pertemuan records for this guru's classes
        $pertemuanDeleted = Pertemuan::whereIn('kelas_id', $kelasIds)->where('pertemuan_number', $num)->delete();

        return redirect()->route('dosen.materis.index')->with('success', "{$deletedCount} materi dari Pertemuan {$num} berhasil dihapus permanen. (Perjalanan pertemuan: {$pertemuanDeleted})");
    }

    /**
     * Create a placeholder Materi for a given pertemuan number so teacher can create pertemuan
     * without filling the materi form immediately.
     */
    public function storePertemuan(Request $request)
    {
        $request->validate([
            'pertemuan_number' => 'required|integer|min:1',
        ]);

        $num = (int) $request->pertemuan_number;

        $kelasIds = auth()->user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
        if (empty($kelasIds)) {
            return response()->json(['error' => 'Anda belum mengajar kelas apapun.'], 403);
        }

        // If the pertemuans table isn't available yet, instruct to run migrations
        if (! Schema::hasTable('pertemuans')) {
            return response()->json(['error' => 'Tabel pertemuans belum ada. Jalankan `php artisan migrate` terlebih dahulu.'], 500);
        }

        // If a pertemuan record or materi already exists for this pertemuan in teacher's classes, don't create duplicate
        $existsPertemuan = Pertemuan::whereIn('kelas_id', $kelasIds)->where('pertemuan_number', $num)->exists();
        $existsMateri = Materi::whereIn('kelas_id', $kelasIds)->where('pertemuan_number', $num)->exists();
        if ($existsPertemuan || $existsMateri) {
            return response()->json(['created' => false, 'message' => 'Pertemuan sudah ada', 'pertemuan' => $num]);
        }

        // Create a pertemuan record for the first class the teacher teaches (keeps materi count 0)
        $kelasId = $kelasIds[0];
        Pertemuan::create([
            'kelas_id' => $kelasId,
            'pertemuan_number' => $num,
        ]);

        return response()->json(['created' => true, 'pertemuan' => $num]);
    }

    /**
     * Display a listing of materi for a specific class and Mata Kuliah.
     */
    public function indexByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check: Ensure the authenticated user teaches this class and Mata Kuliah
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                            ->where('users.id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $materis = Materi::where('kelas_id', $kelas->id)
                         ->where('mata_pelajaran_id', $mataPelajaran->id) // Assuming materi has mata_pelajaran_id
                         ->latest()
                         ->get();

        return view('guru.kelas.mata-pelajaran.materis.index', compact('kelas', 'mataPelajaran', 'materis'));
    }

    /**
     * Show the form for creating a new materi for a specific class and Mata Kuliah.
     */
    public function createByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk membuat materi di sini.');
        }

        return view('guru.kelas.mata-pelajaran.materis.create', compact('kelas', 'mataPelajaran'));
    }

    /**
     * Store a newly created materi for a specific class and Mata Kuliah.
     */
    public function storeByKelasAndMataPelajaran(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan materi di sini.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:51200', // Max 50MB per file
            'link_url' => 'nullable|url',
        ]);

        $materi = new Materi();
        $materi->judul = $request->judul;
        $materi->pertemuan_number = $request->pertemuan_number ?: null;
        $materi->deskripsi = $request->deskripsi;
        $materi->link_url = $request->link_url;
        $materi->kelas_id = $kelas->id;
        $materi->mata_pelajaran_id = $mataPelajaran->id; // Associate with Mata Kuliah
        $materi->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $sanitizedFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $sanitizedFileName . '.' . $extension;

                $path = $file->storeAs('materis/' . $kelas->id . '/' . $mataPelajaran->id, $fileName, 'public');
                
                $materi->materiFiles()->create([
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        return redirect()->route('dosen.kelas.mata-kuliah.materis.index', [$kelas, $mataPelajaran])->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Fetch Mata Kuliah for a given class and authenticated teacher.
     *
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMataPelajaranByKelas(Kelas $kelas)
    {
        $user = auth()->user();
        // Return Mata Kuliah for the kelas. Previously this required the
        // authenticated user to be the teacher for that kelas and filtered
        // results by pivot user_id. For quiz creation we allow fetching the
        // Mata Kuliah for any kelas visible in the form, so return all.
        $mataPelajarans = $kelas->mataPelajaran()->get(['mata_pelajarans.id', 'mata_pelajarans.nama']);

        return response()->json($mataPelajarans);
    }
}
