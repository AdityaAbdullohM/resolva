<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SiswaMataPelajaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        Log::info('Logged in user:', ['user' => $user->toArray()]);

        $kelasYangDiikuti = $user->kelasYangDiikuti()->with('mataPelajaran.materis')->get();
        Log::info('Kelas yang diikuti:', ['kelasYangDiikuti' => $kelasYangDiikuti->toArray()]);

        foreach ($kelasYangDiikuti as $kelas) {
            Log::info('Mata Kuliah for Kelas ' . $kelas->nama . ':', ['mataPelajaran' => $kelas->mataPelajaran->toArray()]);
        }

        return view('siswa.mata-pelajaran.index', compact('kelasYangDiikuti'));
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        // Eager load the necessary relationships
        $mataPelajaran->load(['kelas.guru', 'materis', 'problems', 'pengumumans']);

        return view('siswa.mata-pelajaran.show', compact('mataPelajaran'));
    }
}
