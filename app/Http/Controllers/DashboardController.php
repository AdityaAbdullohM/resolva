<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirect()
    {
        $role = Auth::user()->role;

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'guru':
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'siswa':
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            default:
                return redirect('/login');
        }
    }
}
