<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $kelas = Kelas::with('semester.tahunAjaran')
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('jurusan', 'like', '%' . $search . '%');
            })
            ->paginate(10); // Paginate with 10 items per page

        return view('admin.kelas.index', compact('kelas', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $activeSemester = null;

        if ($activeTahunAjaran) {
            $activeSemester = Semester::where('tahun_ajaran_id', $activeTahunAjaran->id)
                                      ->where('status', 'aktif')
                                      ->first();
        }

        // Jika tidak ada tahun ajaran atau semester aktif, redirect dengan error
        if (!$activeTahunAjaran || !$activeSemester) {
            return redirect()->route('admin.kelas.index')->with('error', 'Tidak ada Tahun Ajaran atau Semester yang aktif. Silakan aktifkan terlebih dahulu.');
        }

        return view('admin.kelas.create', compact('activeTahunAjaran', 'activeSemester'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255|unique:kelas',
            'jurusan' => 'nullable|string|max:255',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran) {
            $validatedData['tahun_ajaran_id'] = $activeTahunAjaran->id;
        }

        Kelas::create($validatedData);

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load('siswa', 'mataPelajaran', 'semester.tahunAjaran');
        $kelas->mataPelajaran->each(function ($mataPelajaran) {
            $mataPelajaran->pivot->load('guru');
        });
        return view('admin.kelas.show', compact('kelas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        // $kelas->load('semester.tahunAjaran', 'mataPelajaran', 'siswa'); // Temporarily remove all relationships
        // $kelas->mataPelajaran->each(function ($mataPelajaran) {
        //     $mataPelajaran->pivot->load('guru');
        // });
        Log::info('Kelas object in edit method:', ['kelas' => $kelas->toArray()]);
        // Log::info('Kelas semester:', ['semester' => optional($kelas->semester)->toArray()]);
        // Log::info('Kelas tahunAjaran:', ['tahunAjaran' => optional(optional($kelas->semester)->tahunAjaran)->toArray()]);
        // Log::info('Kelas mataPelajaran:', ['mataPelajaran' => $kelas->mataPelajaran->toArray()]);

        $mataPelajaranList = MataPelajaran::all();
        $guruList = User::where('role', 'guru')->get();
        $semesters = Semester::with('tahunAjaran')->where('status', 'aktif')->get();

        // Get students currently in this class
        $enrolledStudents = $kelas->siswa()->orderBy('name')->get();

        // Get all students not yet in this class
        $enrolledStudentIds = $enrolledStudents->pluck('id');
        $availableStudents = User::where('role', 'siswa')
                                 ->whereNotIn('id', $enrolledStudentIds)
                                 ->orderBy('name')
                                 ->get();

        return view('admin.kelas.edit', compact('kelas', 'mataPelajaranList', 'guruList', 'semesters', 'enrolledStudents', 'availableStudents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255|unique:kelas,nama,' . $kelas->id,
            'jurusan' => 'nullable|string|max:255',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $semester = Semester::find($validatedData['semester_id']);
        $validatedData['tahun_ajaran_id'] = $semester->tahun_ajaran_id;

        $kelas->update($validatedData);

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas deleted successfully.');
    }

    /**
     * Display the students enrolled in the specified class.
     */
    public function showStudents(Kelas $kelas)
    {
        $kelas->load('siswa'); // Eager load students
        return view('admin.kelas.students', compact('kelas'));
    }

    // Methods for Mata Kuliah Management
    public function addMataPelajaran(Request $request, Kelas $kelas)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($kelas->mataPelajaran()->where('mata_pelajaran_id', $request->mata_pelajaran_id)->exists()) {
            return back()->with('error', 'Mata Kuliah sudah ada di kelas ini.');
        }

        $kelas->mataPelajaran()->attach($request->mata_pelajaran_id, ['user_id' => $request->user_id]);

        return back()->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function removeMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $kelas->mataPelajaran()->detach($mataPelajaran->id);
        return back()->with('success', 'Mata Kuliah berhasil dihapus.');
    }

    public function storeStudent(Request $request, Kelas $kelas)
    {
        $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:users,id',
        ]);

        $kelas->siswa()->syncWithoutDetaching($request->students);

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudent(Kelas $kelas, User $student)
    {
        $kelas->siswa()->detach($student->id);

        return back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }
}