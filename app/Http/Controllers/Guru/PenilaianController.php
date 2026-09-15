<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user();
        $kelasList = $guru->kelasYangDiajar()->get();
        $kelasIds = $kelasList->pluck('id');

        $problemIds = \App\Models\Problem::whereIn('kelas_id', $kelasIds)->pluck('id');

        $query = Submission::whereIn('problem_id', $problemIds)
            ->with(['problem.kelas', 'user']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('problem', function ($q) use ($searchTerm) {
                    $q->where('judul', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Kelas filter
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->whereHas('problem', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('guru.penilaian.index', [
            'submissions' => $submissions,
            'kelasList' => $kelasList,
            'request' => $request,
        ]);
    }
}
