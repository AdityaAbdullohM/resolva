<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function index()
    {
        $guru = Auth::user();
        $submissions = Submission::whereHas('problem', function ($query) use ($guru) {
            $query->where('user_id', $guru->id);
        })->whereNull('nilai')->with('problem', 'user')->latest()->paginate(20);

        return view('guru.submissions.index', compact('submissions'));
    }
}
