<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreForumPostRequest;
use App\Http\Requests\StoreForumThreadRequest;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $instances = Instance::query()
            ->withCount('reunions')
            ->orderBy('name')
            ->get();

        $threads = ForumThread::query()
            ->with([
                'instance',
                'creator' => fn ($q) => $q->withCount('forumPosts'),
                'latestPost.author',
            ])
            ->withCount('posts')
            ->withExists([
                'readBy as is_read' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->orderByDesc('is_pinned')
            ->orderByDesc(
                ForumPost::select('created_at')
                    ->whereColumn('forum_thread_id', 'forum_threads.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        $unreadCount = $threads->filter(fn ($thread) => ! $thread->is_read)->count();

        return view('elus.forum.index', [
            'instances' => $instances,
            'threads' => $threads,
            'currentUser' => $user,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function create(): View
    {
        $instances = Instance::query()->orderBy('name')->get();

        return view('elus.forum.create', [
            'instances' => $instances,
        ]);
    }

    public function store(StoreForumThreadRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validated();

        $thread = DB::transaction(function () use ($user, $validated) {
            $thread = ForumThread::create([
                'instance_id' => $validated['instance_id'],
                'title' => $validated['title'],
                'created_by' => $user->id,
            ]);

            $thread->posts()->create([
                'user_id' => $user->id,
                'body' => $validated['body'],
            ]);

            $thread->readBy()->attach($user->id, ['last_read_at' => now()]);

            return $thread;
        });

        return redirect()->route('elus.forum.show', $thread)
            ->with('success', __('Sujet créé avec succès !'));
    }

    public function show(ForumThread $forumThread): View
    {
        /** @var User $user */
        $user = Auth::user();

        $forumThread->load(['instance', 'creator', 'posts.author']);

        $posts = $forumThread->posts()
            ->with(['author' => fn ($q) => $q->withCount('forumPosts')])
            ->orderBy('created_at')
            ->paginate(30);

        DB::table('forum_thread_user')->updateOrInsert(
            ['forum_thread_id' => $forumThread->id, 'user_id' => $user->id],
            ['last_read_at' => now(), 'updated_at' => now()],
        );

        return view('elus.forum.show', [
            'thread' => $forumThread,
            'posts' => $posts,
            'currentUser' => $user,
        ]);
    }

    public function storePost(StoreForumPostRequest $request, ForumThread $forumThread): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $forumThread->posts()->create([
            'user_id' => $user->id,
            'body' => $request->validated()['body'],
        ]);

        return redirect()->route('elus.forum.show', $forumThread)
            ->with('success', __('Réponse postée avec succès !'));
    }
}
