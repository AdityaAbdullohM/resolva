<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discussion;

class SiswaDiskusiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelasIds = $user->kelas->pluck('id');
        $discussions = Discussion::whereIn('kelas_id', $kelasIds)->get();
        return view('siswa.diskusi.index', compact('discussions'));
    }
}
