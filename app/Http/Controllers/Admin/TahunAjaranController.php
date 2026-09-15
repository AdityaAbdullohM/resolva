<?php

namespace App\Http\Controllers\Admin;

use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $tahunAjarans = TahunAjaran::with('semesters')
            ->when($search, function ($query, $search) {
                $query->where('tahun', 'like', '%' . $search . '%');
            })
            ->paginate(10); // Paginate with 10 items per page

        return view('admin.tahun-ajaran.index', compact('tahunAjarans', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|string|in:Ganjil,Genap',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Find or create the TahunAjaran
                $tahunAjaran = TahunAjaran::firstOrNew(['tahun' => $request->tahun_ajaran]);

                // If a new TahunAjaran is being created or if its status needs to be updated
                if (!$tahunAjaran->exists || $tahunAjaran->status !== $request->status) {
                    // Deactivate all other TahunAjaran if the current one is set to 'aktif'
                    if ($request->status == 'aktif') {
                        TahunAjaran::where('status', 'aktif')->update(['status' => 'tidak_aktif']);
                    }
                    $tahunAjaran->status = $request->status;
                    $tahunAjaran->save();
                }

                // Check if semester already exists for this academic year
                $existingSemester = $tahunAjaran->semesters()->where('nama', $request->semester)->exists();
                if ($existingSemester) {
                    throw new \Exception("Semester {$request->semester} sudah ada untuk tahun ajaran {$request->tahun_ajaran}.");
                }

                // If the new semester is active, deactivate all others
                if ($request->status == 'aktif') {
                    Semester::where('status', 'aktif')->update(['status' => 'tidak_aktif']);
                }

                // Create the new semester
                $tahunAjaran->semesters()->create([
                    'nama' => $request->semester,
                    'status' => $request->status,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }


        return redirect()->route('admin.tahun-ajaran.index')
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
    public function edit(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('semesters');
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_ajaran' => [
                'required',
                'string',
                Rule::unique('tahun_ajarans', 'tahun')->ignore($tahunAjaran->id),
            ],
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        // Jika status diubah menjadi 'aktif', nonaktifkan tahun ajaran lain yang mungkin aktif
        if ($request->status == 'aktif') {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)
                       ->where('status', 'aktif')
                       ->update(['status' => 'tidak_aktif']);
        }

        $tahunAjaran->update($request->all());

        // If the TahunAjaran is set to 'aktif', ensure one of its semesters is also 'aktif'
        if ($request->status == 'aktif') {
            // Deactivate all semesters from all academic years
            Semester::query()->update(['status' => 'tidak_aktif']);

            // Find the first semester for this academic year and set it to 'aktif'
            $firstSemester = $tahunAjaran->semesters()->first();
            if ($firstSemester) {
                $firstSemester->update(['status' => 'aktif']);
            }
        }

        return redirect()->route('admin.tahun-ajaran.index')
                         ->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Pengecekan tambahan, misalnya: jangan hapus jika ada relasi data lain
        // if ($tahunAjaran->semesters()->count() > 0) {
        //     return back()->with('error', 'Tahun Ajaran tidak dapat dihapus karena memiliki semester terkait.');
        // }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
                         ->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
