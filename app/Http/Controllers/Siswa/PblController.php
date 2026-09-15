<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PblValidation;
use App\Models\PblProgress;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PblController extends Controller
{
    public function requestValidation(Request $request, Problem $problem)
    {
        $request->validate([
            'step' => 'required|integer|min:1|max:5',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);

        $step = (int) $request->input('step');
        $groupId = $request->input('group_id');

        // If group_id provided, ensure current user is member of that group
        if ($groupId) {
            $group = \App\Models\Group::find($groupId);
            if (! $group || ! $group->members()->where('users.id', Auth::id())->exists()) {
                return response()->json(['message' => 'not a member of the group'], 403);
            }
        }

        PblValidation::create([
            'problem_id' => $problem->id,
            'user_id' => Auth::id(),
            'group_id' => $groupId,
            'step' => $step,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'requested'], 200);
    }

    public function status(Request $request, Problem $problem)
    {
        $groupId = $request->input('group_id');

        if ($groupId) {
            $progress = PblProgress::where('problem_id', $problem->id)->where('group_id', $groupId)->first();
        } else {
            $progress = PblProgress::where('problem_id', $problem->id)->where('user_id', Auth::id())->first();
        }

        // Pending PBL validations created by student (PblValidation) and pending ProblemStageCompletion
        $pendingQuery = PblValidation::where('problem_id', $problem->id)
            ->where('status', 'pending');

        if ($groupId) {
            $pendingQuery->where('group_id', $groupId);
        } else {
            $pendingQuery->where('user_id', Auth::id());
        }

        $pendingValidations = $pendingQuery->pluck('step')
            ->map(function($s){ return (int) $s; })
            ->unique()
            ->values()
            ->all();

        $pendingStages = \App\Models\ProblemStageCompletion::where('problem_id', $problem->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->pluck('stage')
            ->map(function($s){ return (int) $s; })
            ->unique()
            ->values()
            ->all();

        $pendingSteps = array_values(array_unique(array_merge($pendingValidations, $pendingStages)));

        return response()->json([
            'validated_to' => $progress ? (int) $progress->validated_to : 0,
            'pending_steps' => $pendingSteps,
        ]);
    }
}
