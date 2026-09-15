<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran; // Import TahunAjaran model
use App\Models\Semester;     // Import Semester model
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Validation\Rule; // Import Rule for validation

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'nama' => 'required|string|in:Ganjil,Genap',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        try {
            DB::transaction(function () use ($request, $tahunAjaran) {
                // Check if semester already exists for this academic year
                $existingSemester = $tahunAjaran->semesters()->where('nama', $request->nama)->exists();
                if ($existingSemester) {
                    throw new \Exception("Semester {$request->nama} sudah ada untuk tahun ajaran {$tahunAjaran->tahun}.");
                }

                // If the new semester is active, deactivate all others
                if ($request->status == 'aktif') {
                    Semester::where('status', 'aktif')->update(['status' => 'tidak_aktif']);
                }

                // Create the new semester
                $tahunAjaran->semesters()->create([
                    'nama' => $request->nama,
                    'status' => $request->status,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tahun-ajaran.edit', $tahunAjaran->id)
                         ->with('success', 'Semester berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
