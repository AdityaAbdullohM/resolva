<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Auth::user()->kelasYangDiajar()
            ->with(['mataPelajaran' => function ($query) use ($userId) {
                $query->where('kelas_mata_pelajaran.user_id', $userId);
            }, 'tahunAjaran', 'semester']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }

        $kelasList = $query->latest()->paginate(10);

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $activeSemester = Semester::where('status', 'aktif')->first();

        // Provide Mata Kuliah list so the index view can offer an inline "Tambah Kelas" form
        $mataPelajaranList = MataPelajaran::all();

        return view('guru.kelas.index', compact('kelasList', 'activeTahunAjaran', 'activeSemester', 'mataPelajaranList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $semester = Semester::where('status', 'aktif')->first();
        $mataPelajaranList = MataPelajaran::all(); // Fetch all MataPelajaran

        if (!$tahunAjaran || !$semester) {
            return redirect()->route('dosen.kelas.index')->with('error', 'Tidak ada Tahun Ajaran atau Semester yang aktif. Harap hubungi administrator.');
        }

        return view('guru.kelas.create', compact('tahunAjaran', 'semester', 'mataPelajaranList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
        ]);

        $tahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $semester = Semester::where('status', 'aktif')->first();

        if (!$tahunAjaran || !$semester) {
            return redirect()->route('dosen.kelas.index')->with('error', 'Tidak ada Tahun Ajaran atau Semester yang aktif. Harap hubungi administrator.');
        }

        $kelas = Kelas::create([
            'nama' => $validatedData['nama'],
            'jurusan' => $validatedData['jurusan'],
            'deskripsi' => $validatedData['deskripsi'],
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester_id' => $semester->id,
        ]);

        // Attach the teacher and Mata Kuliah to the pivot table
        $kelas->teachers()->attach(Auth::id(), ['mata_pelajaran_id' => $validatedData['mata_pelajaran_id']]);

        return redirect()->route('dosen.kelas.index')->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        Log::info('Kelas ID received by show method: ' . $kelas->id);

        // Load students, and filter mataPelajaran to only include those taught by the current guru in this class
        $kelas->load([
            'siswa',
            'mataPelajaran' => function ($query) {
                $query->wherePivot('user_id', Auth::id());
            },
            'semester.tahunAjaran'
        ]);

        Log::info('Kelas object in show method: ' . $kelas);
        Log::info('Kelas relations in show method: ' . json_encode($kelas->relationsToArray()));

        // Get all mataPelajaran taught by the current guru in this specific class for the dropdown
        $mataPelajaranDiampu = $kelas->mataPelajaran;

        return view('guru.kelas.show', compact('kelas', 'mataPelajaranDiampu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        if (Auth::user()->role !== 'admin') {
            Log::info('Checking access for Kelas ID: ' . $kelas->id . ' by User ID: ' . Auth::id());
            $teacherClasses = Auth::user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
            Log::info('Classes taught by current user: ' . implode(', ', $teacherClasses));

            if (!in_array($kelas->id, $teacherClasses)) {
                Log::warning('Access denied: User ' . Auth::id() . ' does not teach Kelas ' . $kelas->id);
                abort(403);
            }
        }

        $mataPelajaranList = MataPelajaran::all();
        $guruList = User::where('role', 'guru')->get();

        return view('guru.kelas.edit', compact('kelas', 'mataPelajaranList', 'guruList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        if (Auth::user()->role !== 'admin') {
            Log::info('Checking access for Kelas ID: ' . $kelas->id . ' by User ID: ' . Auth::id());
            $teacherClasses = Auth::user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
            Log::info('Classes taught by current user: ' . implode(', ', $teacherClasses));

            if (!in_array($kelas->id, $teacherClasses)) {
                Log::warning('Access denied: User ' . Auth::id() . ' does not teach Kelas ' . $kelas->id);
                abort(403);
            }
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
        ]);

        $kelas->update([
            'nama' => $validatedData['nama'],
            'jurusan' => $validatedData['jurusan'],
            'deskripsi' => $validatedData['deskripsi'],
        ]);

        // Update the mata_pelajaran_id in the pivot table for the current teacher
        $kelas->teachers()->updateExistingPivot(Auth::id(), ['mata_pelajaran_id' => $validatedData['mata_pelajaran_id']]);

        return redirect()->route('dosen.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        if (Auth::user()->role !== 'admin') {
            Log::info('Checking access for Kelas ID: ' . $kelas->id . ' by User ID: ' . Auth::id());
            $teacherClasses = Auth::user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
            Log::info('Classes taught by current user: ' . implode(', ', $teacherClasses));

            if (!in_array($kelas->id, $teacherClasses)) {
                Log::warning('Access denied: User ' . Auth::id() . ' does not teach Kelas ' . $kelas->id);
                abort(403);
            }
        }

        $kelas->delete();

        return redirect()->route('dosen.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function addStudent(Kelas $kelas)
    {
        // Pastikan hanya guru yang mengajar di kelas ini atau admin yang bisa mengakses
        if (Auth::user()->role !== 'admin' && !$kelas->teachers->contains(Auth::id())) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan siswa ke kelas ini.');
        }

        // Ambil semua siswa yang belum terdaftar di kelas ini
        $students = User::where('role', 'siswa')
                        ->whereDoesntHave('kelas', function ($query) use ($kelas) {
                            $query->where('kelas.id', $kelas->id);
                        })
                        ->orderBy('name')
                        ->get();

        return view('guru.kelas.add_student', compact('kelas', 'students'));
    }

    public function storeStudent(Request $request, Kelas $kelas)
    {
        // Pastikan hanya guru yang mengajar di kelas ini atau admin yang bisa mengakses
        if (Auth::user()->role !== 'admin' && !$kelas->teachers->contains(Auth::id())) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan siswa ke kelas ini.');
        }

        $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:users,id',
        ]);

        // Update kelas_id for each student
        User::whereIn('id', $request->students)->update(['kelas_id' => $kelas->id]);

        return redirect()->route('dosen.kelas.show', $kelas->id)->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudent(Request $request, Kelas $kelas, User $student)
    {
        // Pastikan hanya guru yang mengajar di kelas ini atau admin yang bisa mengakses
        if (Auth::user()->role !== 'admin' && !$kelas->teachers->contains(Auth::id())) {
            abort(403, 'Anda tidak memiliki akses untuk mengeluarkan siswa dari kelas ini.');
        }

        // Pastikan user yang akan dihapus adalah siswa
        if ($student->role !== 'siswa') {
            return redirect()->route('dosen.kelas.show', $kelas->id)->with('error', 'User yang dipilih bukan siswa.');
        }

        // Pastikan siswa ini memang ada di kelas ini sebelum menghapus
        if ($student->kelas_id == $kelas->id) {
            $student->kelas_id = null;
            $student->save();
        }

        return redirect()->route('dosen.kelas.show', $kelas->id)->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function mataPelajaran(Kelas $kelas)
    {
        $mataPelajaran = $kelas->mataPelajaran()->wherePivot('user_id', Auth::id())->get();

        return view('guru.kelas.mata-pelajaran', compact('kelas', 'mataPelajaran'));
    }

    public function anggota(Kelas $kelas)
    {
        $anggota = $kelas->siswa()->paginate(20);

        return view('guru.kelas.anggota', compact('kelas', 'anggota'));
    }
}