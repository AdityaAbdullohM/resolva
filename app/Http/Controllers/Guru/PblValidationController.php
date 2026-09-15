<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PblValidation;
use App\Models\PblProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PblValidationController extends Controller
{
    public function index()
    {
        $pending = PblValidation::where('status', 'pending')->with(['problem', 'user', 'group'])->orderBy('created_at')->get();
        return view('guru.problem-based-learning', ['pending' => $pending]);
    }

    public function validateRequest(Request $request, PblValidation $validation)
    {
        $action = $request->input('action', 'approve');

        if ($action === 'reject' || $action === 'not_validate') {
            $validation->status = 'rejected';
            $validation->validated_by = Auth::id();
            $validation->validated_at = now();
            $validation->save();

            return redirect()->back()->with('success', 'Permintaan tidak divalidasi');
        }

        // default: approve/validate
        $validation->status = 'validated';
        $validation->validated_by = Auth::id();
        $validation->validated_at = now();
        $validation->save();

        // update progress
        if ($validation->group_id) {
            $progress = PblProgress::firstOrNew([
                'problem_id' => $validation->problem_id,
                'group_id' => $validation->group_id,
            ]);
        } else {
            $progress = PblProgress::firstOrNew([
                'problem_id' => $validation->problem_id,
                'user_id' => $validation->user_id,
            ]);
        }
        $progress->validated_to = max($progress->validated_to ?? 0, $validation->step);
        $progress->save();

        return redirect()->back()->with('success', 'Permintaan divalidasi');
    }

    /**
     * Validate all pending requests for problems owned by the current guru.
     */
    public function validateAll(Request $request)
    {
        $pending = PblValidation::where('status', 'pending')
            ->whereHas('problem', function($q){
                $q->where('user_id', Auth::id());
            })->with('problem')->get();

        foreach ($pending as $validation) {
            $validation->status = 'validated';
            $validation->validated_by = Auth::id();
            $validation->validated_at = now();
            $validation->save();

            if ($validation->group_id) {
                $progress = PblProgress::firstOrNew([
                    'problem_id' => $validation->problem_id,
                    'group_id' => $validation->group_id,
                ]);
            } else {
                $progress = PblProgress::firstOrNew([
                    'problem_id' => $validation->problem_id,
                    'user_id' => $validation->user_id,
                ]);
            }
            $progress->validated_to = max($progress->validated_to ?? 0, $validation->step);
            $progress->save();
        }

        return redirect()->back()->with('success', 'Semua permintaan validasi berhasil divalidasi.');
    }
}
