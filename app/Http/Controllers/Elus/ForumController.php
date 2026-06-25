<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreForumPostRequest;
use App\Http\Requests\StoreForumThreadRequest;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Thematique;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $thematiques = Thematique::query()
            ->orderBy('name')
            ->get();

        $query = ForumThread::query()
            ->with([
                'thematique',
                'creator' => fn ($q) => $q->withCount('forumPosts'),
                'latestPost.author',
            ])
            ->withCount('posts')
            ->withExists([
                'readBy as is_read' => fn ($q) => $q->where('user_id', $user->id),
            ]);

        // Filtre par thématique
        if ($request->filled('thematique_id')) {
            $query->where('thematique_id', $request->integer('thematique_id'));
        }

        // Recherche par titre
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        // Tri
        $query->orderByDesc('is_pinned');

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'replies' => $query->orderByDesc(
                ForumPost::selectRaw('count(*)')
                    ->whereColumn('forum_thread_id', 'forum_threads.id')
                    ->groupBy('forum_thread_id')
            ),
            'created' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc(
                ForumPost::select('created_at')
                    ->whereColumn('forum_thread_id', 'forum_threads.id')
                    ->latest()
                    ->limit(1)
            ),
        };

        $threads = $query->paginate(30)->withQueryString();

        $unreadCount = ForumThread::query()
            ->whereDoesntHave('readBy', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('posts')
            ->count();

        return view('elus.forum.index', [
            'thematiques' => $thematiques,
            'threads' => $threads,
            'currentUser' => $user,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function create(): View
    {
        $thematiques = Thematique::query()->orderBy('name')->get();

        return view('elus.forum.create', [
            'thematiques' => $thematiques,
        ]);
    }

    public function store(StoreForumThreadRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validated();

        $thread = DB::transaction(function () use ($user, $validated) {
            $thread = ForumThread::create([
                'thematique_id' => $validated['thematique_id'],
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
            ->with('success', __('Sujet créé avec succès !'))
            ->with('celebrate', true);
    }

    public function show(ForumThread $forumThread): View
    {
        /** @var User $user */
        $user = Auth::user();

        $forumThread->load(['thematique', 'creator', 'posts.author']);

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
            ->with('success', __('Réponse postée avec succès !'))
            ->with('celebrate', true);
    }
}
