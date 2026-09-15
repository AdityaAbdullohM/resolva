<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['user', 'problem.kelas'])->latest()->paginate(20);
        return view('admin.submissions.index', compact('submissions'));
    }
}
