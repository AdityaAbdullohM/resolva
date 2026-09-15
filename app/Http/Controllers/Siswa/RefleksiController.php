<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Refleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefleksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $refleksis = Refleksi::where('user_id', Auth::id())->with('problem')->get();
        return view('siswa.refleksi.index', compact('refleksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $problems = Auth::user()->problems;
        return view('siswa.refleksi.create', compact('problems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'problem_id' => 'required|exists:problems,id',
            'content' => 'required|string',
        ]);

        Refleksi::create([
            'user_id' => Auth::id(),
            'problem_id' => $request->problem_id,
            'content' => $request->content,
        ]);

        return redirect()->route('mahasiswa.refleksi.index')->with('success', 'Refleksi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Refleksi $refleksi)
    {
        $this->authorize('view', $refleksi);
        return view('siswa.refleksi.show', compact('refleksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Refleksi $refleksi)
    {
        $this->authorize('update', $refleksi);
        $problems = Auth::user()->problems;
        return view('siswa.refleksi.edit', compact('refleksi', 'problems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Refleksi $refleksi)
    {
        $this->authorize('update', $refleksi);

        $request->validate([
            'content' => 'required|string',
        ]);

        $refleksi->update([
            'content' => $request->content,
        ]);

        return redirect()->route('mahasiswa.refleksi.index')->with('success', 'Refleksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Refleksi $refleksi)
    {
        $this->authorize('delete', $refleksi);
        $refleksi->delete();
        return redirect()->route('mahasiswa.refleksi.index')->with('success', 'Refleksi berhasil dihapus.');
    }
}
