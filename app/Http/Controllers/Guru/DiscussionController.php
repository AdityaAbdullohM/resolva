<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\DiscussionPost;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    /**
     * Display a listing of all discussions for the teacher.
     */
    public function indexAll()
    {
        // Get the current teacher
        $teacher = Auth::user();

        // Get the IDs of the classes the teacher teaches
        $kelasIds = $teacher->kelasYangDiajar()->pluck('kelas.id');

        // Get the IDs of the problems associated with those classes
        $problemIds = Problem::whereIn('kelas_id', $kelasIds)->pluck('id');

        // Get discussions related to those problems
        $discussions = Discussion::whereIn('problem_id', $problemIds)
            ->with('user', 'problem') // Eager load user and problem
            ->latest()
            ->paginate(10);

        return view('guru.discussions.index', compact('discussions'));
    }

    /**
     * Show the form for creating a new discussion without a specific problem context.
     */
    public function createGeneral()
    {
        $teacher = Auth::user();

        // Get the IDs of the classes the teacher teaches
        $kelasIds = $teacher->kelasYangDiajar()->pluck('kelas.id');

        // Get problems associated with those classes
        $problems = Problem::whereIn('kelas_id', $kelasIds)
            ->with(['kelas'])
            ->latest('created_at')
            ->get();

        return view('guru.discussions.create_general', compact('problems'));
    }

    /**
     * Store a newly created discussion from the general form.
     */
    public function storeGeneral(Request $request)
    {
        $validatedData = $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $problem = Problem::findOrFail($validatedData['problem_id']);

        // Authorization check: ensure the authenticated user teaches the class of the problem
        $this->authorize('view', $problem);

        $discussion = $problem->discussions()->create([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
            'kelas_id' => $problem->kelas_id,
        ]);

        return redirect()->route('dosen.discussions.show', $discussion)->with('success', 'Diskusi berhasil dibuat.');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Problem $problem)
    {
        $discussions = $problem->discussions()->with('user')->latest()->paginate(10);
        return view('guru.discussions.index_problem', compact('problem', 'discussions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Problem $problem)
    {
        if (Auth::user()->role == 'siswa') {
            return view('siswa.discussions.create', compact('problem'));
        }
        return view('guru.discussions.create', compact('problem'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Problem $problem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $discussion = $problem->discussions()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'kelas_id' => $problem->kelas_id,
        ]);

        if (Auth::user()->role == 'siswa') {
            return redirect()->route('mahasiswa.discussions.show', $discussion);
        }

        return redirect()->route('dosen.discussions.show', $discussion);
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion)
    {
        $discussion->load(['user', 'posts.user', 'posts.replies.user']);

        if (Auth::user()->role == 'siswa') {
            return view('siswa.discussions.show', compact('discussion'));
        }

        return view('guru.discussions.show', compact('discussion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discussion $discussion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discussion $discussion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion)
    {
        //
    }

    public function storePost(Request $request, Discussion $discussion)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:discussion_posts,id',
        ]);

        $discussion->posts()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Your reply has been posted.');
    }

    /**
     * Display a listing of discussions for a specific class and Mata Kuliah.
     */
    public function indexByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check: Ensure the authenticated user teaches this class and Mata Kuliah
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke diskusi ini.');
        }

        // Get problems associated with this class and Mata Kuliah
        $problemIds = Problem::where('kelas_id', $kelas->id)
                             ->where('mata_pelajaran_id', $mataPelajaran->id)
                             ->pluck('id');

        $discussions = Discussion::whereIn('problem_id', $problemIds)
                                 ->with('user', 'problem')
                                 ->latest()
                                 ->paginate(10);

        return view('guru.kelas.mata-pelajaran.discussions.index', compact('kelas', 'mataPelajaran', 'discussions'));
    }

    /**
     * Show the form for creating a new discussion for a specific class and Mata Kuliah.
     */
    public function createByKelasAndMataPelajaran(Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk membuat diskusi di sini.');
        }

        // Get problems associated with this class and Mata Kuliah to allow selection
        $problems = Problem::where('kelas_id', $kelas->id)
                           ->where('mata_pelajaran_id', $mataPelajaran->id)
                           ->get();

        return view('guru.kelas.mata-pelajaran.discussions.create', compact('kelas', 'mataPelajaran', 'problems'));
    }

    /**
     * Store a newly created discussion for a specific class and Mata Kuliah.
     */
    public function storeByKelasAndMataPelajaran(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        // Authorization check
        $isTeacherOfClassAndMataPelajaran = $kelas->teachers()
                                                ->where('user_id', auth()->id())
                                                ->wherePivot('mata_pelajaran_id', $mataPelajaran->id)
                                                ->exists();

        if (!$isTeacherOfClassAndMataPelajaran && auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan diskusi di sini.');
        }

        $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Ensure the selected problem belongs to the specified class and Mata Kuliah
        $problem = Problem::where('id', $request->problem_id)
                          ->where('kelas_id', $kelas->id)
                          ->where('mata_pelajaran_id', $mataPelajaran->id)
                          ->firstOrFail();

        $discussion = $problem->discussions()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'kelas_id' => $problem->kelas_id,
        ]);

        return redirect()->route('dosen.kelas.mata-kuliah.discussions.index', [$kelas, $mataPelajaran])->with('success', 'Diskusi berhasil dibuat.');
    }
}