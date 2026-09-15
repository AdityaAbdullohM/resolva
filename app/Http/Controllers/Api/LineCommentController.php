<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LineComment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LineCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Submission $submission)
    {
        $comments = $submission->lineComments()->with('user')->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Submission $submission)
    {
        try {
            $validatedData = $request->validate([
                'line_number' => 'required|integer|min:1',
                'comment' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $comment = $submission->lineComments()->create([
            'user_id' => Auth::id(),
            'line_number' => $validatedData['line_number'],
            'comment' => $validatedData['comment'],
        ]);

        return response()->json($comment->load('user'), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LineComment $lineComment)
    {
        if ($lineComment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validatedData = $request->validate([
                'comment' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $lineComment->update($validatedData);

        return response()->json($lineComment->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LineComment $lineComment)
    {
        if ($lineComment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lineComment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
