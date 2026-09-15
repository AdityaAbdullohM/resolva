<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Kelas;

class ProblemController extends Controller
{
    public function show(Kelas $kelas, Problem $problem)
    {
        return view('siswa.problem.show', compact('kelas', 'problem'));
    }
}
