<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::with('kelas')->latest()->paginate(20);
        return view('admin.problems.index', compact('problems'));
    }
}
