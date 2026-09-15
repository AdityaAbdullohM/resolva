<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::user();
        $mataPelajaranIds = optional($siswa->kelasYangDiikuti)->flatMap(function ($kelas) {
            return $kelas->mataPelajaran->pluck('id');
        })->unique() ?? collect([]);

        $query = Pengumuman::where(function ($query) use ($mataPelajaranIds) {
            $query->whereIn('mata_pelajaran_id', $mataPelajaranIds)
                  ->orWhereNull('mata_pelajaran_id');
        })
        ->where('published_at', '<=', now());

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $pengumumans = $query->latest()->paginate(10);

        return view('siswa.pengumumans.index', compact('pengumumans'));
    }

    public function show(Pengumuman $pengumuman)
    {
        return view('siswa.pengumumans.show', compact('pengumuman'));
    }
}
