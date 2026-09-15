<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $mataPelajarans = MataPelajaran::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->paginate(10); // Paginate with 10 items per page

        return view('admin.mata-pelajaran.index', compact('mataPelajarans', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mata-pelajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mata_pelajarans',
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('admin.mata-kuliah.index')
                         ->with('success', 'Mata Kuliah created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.show', compact('mataPelajaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mata_pelajarans,nama,' . $mataPelajaran->id,
        ]);

        $mataPelajaran->update($request->all());

        return redirect()->route('admin.mata-kuliah.index')
                         ->with('success', 'Mata Kuliah updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return redirect()->route('admin.mata-kuliah.index')
                         ->with('success', 'Mata Kuliah deleted successfully.');
    }
}
