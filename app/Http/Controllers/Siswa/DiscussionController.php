<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\Problem;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        // If a specific group is requested, show only discussions for that group
        if ($request->filled('group_id')) {
            $group = Group::find($request->input('group_id'));
            if ($group) {
                $isMember = $group->members()->where('user_id', Auth::id())->exists();

                $discussions = Discussion::where('group_id', $group->id)->with(['user', 'problem', 'posts'])->latest()->paginate(10);

                // single discussion thread for the group (used as message history)
                $groupDiscussion = Discussion::where('group_id', $group->id)->with(['posts.user', 'user'])->first();

                if (!$groupDiscussion) {
                    // Provide an empty Discussion instance so the view renders the group feed and composer
                    $groupDiscussion = new Discussion(['group_id' => $group->id]);
                    $groupDiscussion->setRelation('posts', collect());
                    $groupDiscussion->setRelation('user', null);
                }

                return view('siswa.discussions.index', compact('discussions', 'group', 'isMember', 'groupDiscussion'));
            }
            // if group not found, fall through to default behavior
        }
        $siswa = Auth::user();
        // Prefer the student's primary kelas (if set), otherwise use kelas they follow
        if ($siswa->kelas_id) {
            $kelasIds = [$siswa->kelas_id];
        } else {
            $kelasIds = $siswa->kelasYangDiikuti()->pluck('id')->toArray();
        }

        // If no kelas found, return empty paginator
        if (empty($kelasIds)) {
            $discussions = Discussion::whereNull('id')->paginate(10);
            return view('siswa.discussions.index', compact('discussions'));
        }

        // Prefer filtering directly by discussion.kelas_id (populated on create), fallback to problem relation
        $query = Discussion::whereIn('kelas_id', $kelasIds)->orWhereHas('problem', function ($q) use ($kelasIds) {
            $q->whereIn('kelas_id', $kelasIds);
        });

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $discussions = $query->with(['user', 'problem', 'posts'])->latest()->paginate(10);

        return view('siswa.discussions.index', compact('discussions'));
    }

    public function create(Problem $problem)
    {
        return view('siswa.discussions.create', compact('problem'));
    }

    public function storeGeneral(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'group_id' => 'nullable|exists:groups,id'
        ]);

        $groupId = $request->input('group_id');

        // If posting to a group, prefer adding a post to the existing group discussion
        if ($groupId) {
            $group = Group::find($groupId);

            // derive kelas id from group's problem when possible
            $kelasId = $group && $group->problem ? ($group->problem->kelas_id ?? null) : (Auth::user()->kelas_id ?: null);

            // find any existing discussion linked to this group (don't require problem_id to be null)
            $groupDiscussion = Discussion::where('group_id', $groupId)->first();

            if ($groupDiscussion) {
                // create a top-level post in the existing discussion
                $groupDiscussion->posts()->create([
                    'content' => $request->input('content'),
                    'user_id' => Auth::id(),
                ]);

                return redirect()->route('mahasiswa.discussions.index', ['group_id' => $groupId]);
            }

            // No existing group discussion: create one with the message as the opening content
            $title = $request->input('title') ?: (Str::limit(strip_tags($request->input('content')), 50, '')) ?: 'Diskusi Kelompok';

            $discussion = Discussion::create([
                'title' => $title,
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'kelas_id' => $kelasId,
                'group_id' => $groupId,
            ]);

            return redirect()->route('mahasiswa.discussions.index', ['group_id' => $groupId]);
        }

        // Non-group general discussion: create a standalone discussion as before
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $siswa = Auth::user();
        $kelasId = $siswa->kelas_id ?: null;

        $discussion = Discussion::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'kelas_id' => $kelasId,
        ]);

        return redirect()->route('mahasiswa.discussions.show', $discussion);
    }

    public function store(Request $request, Problem $problem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $discussion = $problem->discussions()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'kelas_id' => $problem->kelas_id,
        ]);

        return redirect()->route('mahasiswa.discussions.show', $discussion);
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['user', 'posts.user', 'posts.replies.user']);
        return view('siswa.discussions.show', compact('discussion'));
    }

    public function storePost(Request $request, Discussion $discussion)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:discussion_posts,id',
        ]);

        $discussion->posts()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}
