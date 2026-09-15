<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $groups = Group::where('user_id', $user->id)
                       ->with('problem', 'members')
                       ->paginate(9); // 9 items per page for a 3-column grid
        
        $problems = $user->problems()->get();

        return view('guru.groups.index', compact('groups', 'problems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $problems = Auth::user()->problems()->get(); // Assuming a teacher can only create groups for their own problems
        // Show only students from classes the logged-in teacher teaches
        // Exclude students already in any group created by this teacher
        $kelasIds = Auth::user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
        $teacherId = Auth::id();
        $students = User::where('role', 'siswa')
                        ->whereHas('kelas', function($q) use ($kelasIds){
                            $q->whereIn('id', $kelasIds);
                        })
                        ->whereDoesntHave('groups', function($q) use ($teacherId){
                            $q->where('groups.user_id', $teacherId);
                        })->get();
        return view('guru.groups.create', compact('problems', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'problem_id' => 'nullable|exists:problems,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $group = Group::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'problem_id' => $validatedData['problem_id'] ?? null,
            'user_id' => Auth::id(), // Teacher who created the group
        ]);

        if (isset($validatedData['members'])) {
            $group->members()->attach($validatedData['members']);
        }

        return redirect()->route('dosen.groups.index')->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        if (! $this->canAccess($group)) {
            abort(403, 'Unauthorized action.');
        }

        $group->load('problem', 'members');
        return view('guru.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        if (! $this->canAccess($group)) {
            abort(403, 'Unauthorized action.');
        }

        $problems = Auth::user()->problems()->get();
        // Show only students from classes the logged-in teacher teaches
        // Include students who are already members of this group (so they remain selectable)
        $kelasIds = Auth::user()->kelasYangDiajar()->pluck('kelas.id')->toArray();
        $teacherId = Auth::id();
        $students = User::where('role', 'siswa')
                        ->whereHas('kelas', function($q) use ($kelasIds){
                            $q->whereIn('id', $kelasIds);
                        })
                        ->where(function($q) use ($teacherId, $group){
                            $q->whereDoesntHave('groups', function($q2) use ($teacherId){
                                $q2->where('groups.user_id', $teacherId);
                            })->orWhereHas('groups', function($q3) use ($group){
                                $q3->where('groups.id', $group->id);
                            });
                        })->get();
        $group->load('members'); // Load existing members

        return view('guru.groups.edit', compact('group', 'problems', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        if (! $this->canAccess($group)) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'problem_id' => 'nullable|exists:problems,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $group->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'problem_id' => $validatedData['problem_id'] ?? null,
        ]);

        $group->members()->sync($validatedData['members'] ?? []);

        return redirect()->route('dosen.groups.index')->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if (! $this->canAccess($group)) {
            abort(403, 'Unauthorized action.');
        }

        $group->delete();

        return redirect()->route('dosen.groups.index')->with('success', 'Group deleted successfully!');
    }

    /**
     * Determine if the authenticated user may access the given group.
     * Allow if the user is owner, an admin, or a teacher for the class the group's problem belongs to.
     */
    private function canAccess(Group $group): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Owner or admin
        if ($group->user_id === $user->id || $user->isAdmin()) {
            return true;
        }

        // If group is linked to a problem, allow if the user teaches that kelas
        if ($group->problem) {
            $kelas = $group->problem->kelas;
            if ($kelas && $user->isTeacherOrAdminForKelas($kelas)) {
                return true;
            }
        }

        return false;
    }

}
