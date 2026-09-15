<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Problem;

class SiswaTugasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelasIds = $user->kelas->pluck('id');
        $problems = Problem::whereIn('kelas_id', $kelasIds)->get();
        return view('siswa.tugas.index', compact('problems'));
    }
}
