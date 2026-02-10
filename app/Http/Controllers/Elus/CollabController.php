<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CollabController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $conversations = Conversation::query()
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->with(['users', 'latestMessage.sender'])
            ->withCount([
                'messages as unread_count' => fn ($query) => $query
                    ->whereNull('read_at')
                    ->where('user_id', '!=', $user->id),
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        $recipients = User::query()
            ->whereKeyNot($user->id)
            ->where(function ($query) {
                $query->where('is_elu', true)->orWhere('is_admin', true);
            })
            ->orderBy('name')
            ->get();

        return view('elus.collab.index', [
            'conversations' => $conversations,
            'recipients' => $recipients,
            'currentUser' => $user,
        ]);
    }

    public function storeConversation(StoreConversationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $recipientId = (int) $request->validated()['recipient_id'];

        $existingConversation = Conversation::query()
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->whereHas('users', fn ($query) => $query->where('users.id', $recipientId))
            ->whereDoesntHave('users', fn ($query) => $query->whereNotIn('users.id', [$user->id, $recipientId]))
            ->first();

        if ($existingConversation) {
            return redirect()->route('elus.collab.show', $existingConversation);
        }

        $conversation = DB::transaction(function () use ($user, $recipientId) {
            $conversation = Conversation::create([
                'created_by' => $user->id,
            ]);

            $conversation->users()->attach([$user->id, $recipientId]);

            return $conversation;
        });

        return redirect()->route('elus.collab.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $conversation->users()->whereKey($user->id)->exists()) {
            abort(403, __('Accès réservé aux élus.'));
        }

        $conversation->load('users');

        Message::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where('user_id', '!=', $user->id)
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(30);

        return view('elus.collab.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherUser' => $conversation->otherParticipant($user),
            'currentUser' => $user,
        ]);
    }

    public function storeMessage(StoreMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $conversation->users()->whereKey($user->id)->exists()) {
            abort(403, __('Accès réservé aux élus.'));
        }

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $request->validated()['body'],
        ]);

        $conversation->forceFill(['last_message_at' => $message->created_at])->save();

        return redirect()->route('elus.collab.show', $conversation);
    }
}
