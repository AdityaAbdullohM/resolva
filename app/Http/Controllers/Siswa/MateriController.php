<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\MateriFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Pertemuan;

class MateriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $semesterAktif = $tahunAjaranAktif->semesters()->where('status', 'aktif')->first();

        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif pada tahun ajaran ini.');
        }

        // Collect kelas IDs where the student is enrolled.
        // Some kelas records may not have tahun_ajaran_id/semester_id set,
        // so include the student's assigned kelas and any joined classes
        // regardless of those fields to avoid hiding materi unintentionally.
        $kelasIds = collect();

        // If the user has a primary kelas_id (siswa assigned directly to a kelas)
        if ($user->kelas_id) {
            $primary = Kelas::where('id', $user->kelas_id)->pluck('id');
            $kelasIds = $kelasIds->merge($primary);
        }

        // Also include any kelas from many-to-many enrollments (kelas_user pivot)
        if (method_exists($user, 'kelasYangDiikuti')) {
            $joined = $user->kelasYangDiikuti()->pluck('kelas.id');
            $kelasIds = $kelasIds->merge($joined);
        }

        $kelasIds = $kelasIds->unique()->values()->all();

        $materis = collect();
        if (!empty($kelasIds)) {
            $materis = Materi::whereIn('kelas_id', $kelasIds)
                        ->with(['kelas', 'mataPelajaran'])
                        ->latest()
                        ->paginate(10);
        } else {
            // Return empty paginator when student not enrolled in any kelas for the active year/semester
            $materis = Materi::whereRaw('0 = 1')->paginate(10);
        }

        // Also fetch pertemuan entries for classes the student is enrolled in
        $pertemuans = Pertemuan::whereIn('kelas_id', $kelasIds)
                ->orderBy('pertemuan_number')
                ->get();

        if ($pertemuans->isEmpty()) {
            $materiPertemuanNumbers = Materi::whereIn('kelas_id', $kelasIds)
                    ->whereNotNull('pertemuan_number')
                    ->distinct()
                    ->orderBy('pertemuan_number')
                    ->pluck('pertemuan_number')
                    ->toArray();

            foreach ($materiPertemuanNumbers as $number) {
                $obj = new \stdClass();
                $obj->pertemuan_number = $number;
                $obj->judul = 'Pertemuan ' . $number;
                $obj->nama = null;
                $obj->deskripsi = null;
                $obj->created_at = Materi::whereIn('kelas_id', $kelasIds)
                                        ->where('pertemuan_number', $number)
                                        ->orderBy('created_at')
                                        ->value('created_at') ?? now();
                $pertemuans->push($obj);
            }
        }

        // counts of materi per pertemuan_number (null/empty treated as 'Umum')
        $pertemuanCounts = Materi::whereIn('kelas_id', $kelasIds)
                    ->selectRaw("COALESCE(pertemuan_number, 0) as pernum, count(*) as cnt")
                    ->groupBy('pernum')
                    ->pluck('cnt', 'pernum')
                    ->toArray();

        // Build grouping to support pertemuan filter (label form: 'Pertemuan X' or 'Umum')
        $collection = ($materis instanceof \Illuminate\Pagination\AbstractPaginator) ? $materis->getCollection() : collect($materis);
        $groups = $collection->groupBy(function($m){
            if (!empty($m->pertemuan_number)) {
                return 'Pertemuan ' . $m->pertemuan_number;
            }
            if (preg_match('/^(Pertemuan\s*\d+)/i', $m->judul ?? '', $matches)) {
                return $matches[1];
            }
            return 'Umum';
        });

        $selected = request('pertemuan');
        $selectedItems = null;
        if ($selected) {
            $selectedItems = $groups->get($selected, collect());
        }

        return view('siswa.materis.index', compact('materis', 'tahunAjaranAktif', 'semesterAktif', 'pertemuans', 'pertemuanCounts', 'selected', 'selectedItems'));
    }

    public function show(Kelas $kelas, Materi $materi)
    {
        $user = Auth::user();

        // Check if the user is a student and is enrolled in the class
        if ($user->hasRole('siswa')) {
            $isEnrolled = false;

            // If the user has a primary `kelas_id`, allow access
            if ($user->kelas_id == $kelas->id) {
                $isEnrolled = true;
            } else {
                // Also check many-to-many enrollment (kelas_user)
                $isEnrolled = $user->kelasYangDiikuti()->where('kelas_id', $kelas->id)->exists();
            }

            if (! $isEnrolled) {
                abort(403, 'Anda tidak memiliki akses untuk melihat materi ini.');
            }
        }

        // Ensure the materi belongs to the provided kelas
        if ((int) $materi->kelas_id !== (int) $kelas->id) {
            abort(404, 'Materi tidak ditemukan di kelas ini.');
        }

        $materi->load('materiFiles');

        return view('siswa.materis.show', compact('materi', 'kelas'));
    }

    public function viewFile(Kelas $kelas, Materi $materi, MateriFile $materiFile)
    {
        // Ensure the file belongs to the materi
        Log::info('MateriFile ID: ' . $materiFile->id);
        Log::info('MateriFile file_path (original): ' . $materiFile->file_path);

        // Prefer Storage helper to resolve the actual filesystem path used by Storage::download
        if (Storage::exists($materiFile->file_path)) {
            try {
                $absolute = Storage::path($materiFile->file_path);
                if (File::exists($absolute)) {
                    return response()->file($absolute);
                }
            } catch (\Exception $e) {
                Log::warning('Storage::path failed for materiFile id ' . $materiFile->id . ': ' . $e->getMessage());
            }
        }

        // Fallback: try common storage locations
        $originalPath = storage_path('app/' . $materiFile->file_path);
        $cleanPath = str_replace('public/', '', $materiFile->file_path);
        $publicPath = storage_path('app/public/' . $cleanPath);
        $altPath = storage_path('app/' . $cleanPath);

        $pathsToTry = [$originalPath, $publicPath, $altPath];

        foreach ($pathsToTry as $p) {
            Log::info('Checking path: ' . $p);
            if (File::exists($p)) {
                return response()->file($p);
            }
        }

        Log::warning('File not found. Tried paths: ' . implode(', ', $pathsToTry));
        abort(404);
    }

    public function downloadFile(Kelas $kelas, Materi $materi, MateriFile $materiFile)
    {
        // Implement authorization if needed
        // $this->authorize('view', $materi);

        if (Storage::exists($materiFile->file_path)) {
            try {
                $absolute = Storage::path($materiFile->file_path);
                if (File::exists($absolute)) {
                    return response()->download($absolute, $materiFile->original_name ?? $materiFile->original_name ?? basename($absolute));
                }
            } catch (\Exception $e) {
                Log::warning('Storage::path failed for materiFile id ' . $materiFile->id . ': ' . $e->getMessage());
            }
        }

        // Fallback
        $originalPath = storage_path('app/' . $materiFile->file_path);
        $cleanPath = str_replace('public/', '', $materiFile->file_path);
        $publicPath = storage_path('app/public/' . $cleanPath);
        $altPath = storage_path('app/' . $cleanPath);

        $pathsToTry = [$originalPath, $publicPath, $altPath];

        foreach ($pathsToTry as $p) {
            if (File::exists($p)) {
                return response()->download($p, $materiFile->original_name ?? basename($p));
            }
        }

        abort(404);
    }
}