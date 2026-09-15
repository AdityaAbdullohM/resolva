<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaKelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = $user->kelasYangDiikuti()->with('mataPelajaran')->get();
        return view('siswa.kelas.index', compact('kelas'));
    }
}