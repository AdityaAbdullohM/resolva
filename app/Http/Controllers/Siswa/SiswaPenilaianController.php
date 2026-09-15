<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaPenilaianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $submissions = $user->submissions()->with('problem.mataPelajaran')->get();
        return view('siswa.penilaian.index', compact('submissions'));
    }
}
