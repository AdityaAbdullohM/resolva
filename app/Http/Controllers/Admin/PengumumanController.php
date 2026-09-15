<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // Admin pengumuman feature removed — stub controller
    public function __call($method, $parameters)
    {
        return redirect()->route('admin.dashboard')->with('info', 'Fitur Pengumuman untuk admin telah dihapus.');
    }
}
