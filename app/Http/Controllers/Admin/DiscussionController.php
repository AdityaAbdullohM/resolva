<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $query = Discussion::with(['user', 'problem']);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $discussions = $query->latest()->paginate(10);

        return view('admin.discussions.index', compact('discussions'));
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['user', 'problem', 'posts.user']);
        return view('admin.discussions.show', compact('discussion'));
    }
}
