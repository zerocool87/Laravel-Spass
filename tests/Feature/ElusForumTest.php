<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Thematique;
use App\Models\User;
use Tests\TestCase;

class ElusForumTest extends TestCase
{
    public function test_non_elu_cannot_access_forum(): void
    {
        $user = User::factory()->create(['is_elu' => false, 'is_admin' => false]);

        $this->actingAs($user)
            ->get(route('elus.forum.index'))
            ->assertStatus(403);
    }

    public function test_elu_can_view_empty_forum(): void
    {
        $user = User::factory()->create(['is_elu' => true]);

        $this->actingAs($user)
            ->get(route('elus.forum.index'))
            ->assertOk()
            ->assertSee(__('Aucune discussion'));
    }

    public function test_elu_can_create_thread_with_first_post(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematique = Thematique::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('elus.forum.store'), [
                'thematique_id' => $thematique->id,
                'title' => 'Sujet test',
                'body' => 'Premier message du sujet',
            ]);

        $thread = ForumThread::first();

        $this->assertNotNull($thread);
        $this->assertEquals('Sujet test', $thread->title);
        $this->assertEquals($thematique->id, $thread->thematique_id);
        $this->assertEquals($user->id, $thread->created_by);

        $this->assertDatabaseHas('forum_posts', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Premier message du sujet',
        ]);

        $response->assertRedirect(route('elus.forum.show', $thread));
    }

    public function test_elu_can_reply_to_thread(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('elus.forum.posts.store', $thread), [
                'body' => 'Ma réponse',
            ]);

        $this->assertDatabaseHas('forum_posts', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Ma réponse',
        ]);

        $response->assertRedirect(route('elus.forum.show', $thread));
    }

    public function test_elu_can_view_thread_with_posts(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()
            ->hasPosts(3, ['user_id' => $user->id])
            ->create();

        $this->actingAs($user)
            ->get(route('elus.forum.show', $thread))
            ->assertOk()
            ->assertSee($thread->title);
    }

    public function test_non_elu_cannot_create_thread(): void
    {
        $user = User::factory()->create(['is_elu' => false, 'is_admin' => false]);
        $thematique = Thematique::factory()->create();

        $this->actingAs($user)
            ->post(route('elus.forum.store'), [
                'thematique_id' => $thematique->id,
                'title' => 'Test',
                'body' => 'Test',
            ])
            ->assertStatus(403);
    }

    public function test_threads_listed_in_table_on_index(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematiqueA = Thematique::factory()->create(['name' => 'Commission A']);
        $thematiqueB = Thematique::factory()->create(['name' => 'Commission B']);

        ForumThread::factory()->create(['thematique_id' => $thematiqueA->id, 'title' => 'Sujet A']);
        ForumThread::factory()->create(['thematique_id' => $thematiqueB->id, 'title' => 'Sujet B']);

        $this->actingAs($user)
            ->get(route('elus.forum.index'))
            ->assertOk()
            ->assertSee('Commission A')
            ->assertSee('Commission B')
            ->assertSee('Sujet A')
            ->assertSee('Sujet B')
            ->assertSeeInOrder([__('Thématique'), __('Sujet'), __('Auteur'), __('Rép.'), __('Dernière activité')]);
    }

    public function test_thread_show_marks_as_read(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()->create();

        $this->actingAs($user)
            ->get(route('elus.forum.show', $thread));

        $this->assertDatabaseHas('forum_thread_user', [
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_index_paginates_at_7(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematique = Thematique::factory()->create();

        ForumThread::factory()
            ->count(8)
            ->create(['thematique_id' => $thematique->id]);

        $response = $this->actingAs($user)
            ->get(route('elus.forum.index'))
            ->assertOk();

        $response->assertViewHas('threads', fn ($paginator) => $paginator->hasPages());

        $response = $this->actingAs($user)
            ->get(route('elus.forum.index', ['page' => 2]))
            ->assertOk();

        $response->assertViewHas('threads', fn ($paginator) => $paginator->currentPage() === 2);
    }

    public function test_index_search_by_title(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematique = Thematique::factory()->create();

        ForumThread::factory()->create([
            'thematique_id' => $thematique->id,
            'title' => 'Budget participatif 2026',
        ]);
        ForumThread::factory()->create([
            'thematique_id' => $thematique->id,
            'title' => 'Aménagement du parc',
        ]);

        $this->actingAs($user)
            ->get(route('elus.forum.index', ['search' => 'Budget']))
            ->assertOk()
            ->assertSee('Budget participatif 2026')
            ->assertDontSee('Aménagement du parc');
    }

    public function test_index_filter_by_instance(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematiqueA = Thematique::factory()->create(['name' => 'Commission Eau']);
        $thematiqueB = Thematique::factory()->create(['name' => 'Commission Voirie']);

        ForumThread::factory()->create([
            'thematique_id' => $thematiqueA->id,
            'title' => 'Sujet Eau',
        ]);
        ForumThread::factory()->create([
            'thematique_id' => $thematiqueB->id,
            'title' => 'Sujet Voirie',
        ]);

        $this->actingAs($user)
            ->get(route('elus.forum.index', ['thematique_id' => $thematiqueA->id]))
            ->assertOk()
            ->assertSee('Sujet Eau')
            ->assertDontSee('Sujet Voirie');
    }

    public function test_index_sort_by_replies(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thematique = Thematique::factory()->create();

        $threadMany = ForumThread::factory()->create([
            'thematique_id' => $thematique->id,
            'title' => 'Très actif',
        ]);
        ForumPost::factory()->count(5)->create([
            'forum_thread_id' => $threadMany->id,
        ]);

        $threadFew = ForumThread::factory()->create([
            'thematique_id' => $thematique->id,
            'title' => 'Peu actif',
        ]);
        ForumPost::factory()->create([
            'forum_thread_id' => $threadFew->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('elus.forum.index', ['sort' => 'replies']))
            ->assertOk();

        $response->assertSeeInOrder(['Très actif', 'Peu actif']);
    }

    public function test_elu_can_reply_to_specific_post(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $originalPost = ForumPost::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('elus.forum.posts.store', $originalPost->thread), [
                'body' => 'Ma réponse spécifique',
                'reply_to_post_id' => $originalPost->id,
            ]);

        $response->assertRedirect(route('elus.forum.show', $originalPost->thread));

        $this->assertDatabaseHas('forum_posts', [
            'body' => 'Ma réponse spécifique',
            'reply_to_post_id' => $originalPost->id,
        ]);
    }

    public function test_reply_to_post_shows_citation_in_view(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $originalAuthor = User::factory()->create(['is_elu' => true, 'name' => 'Original Author']);
        $originalPost = ForumPost::factory()->create([
            'user_id' => $originalAuthor->id,
            'body' => 'Message original',
        ]);

        ForumPost::factory()->create([
            'forum_thread_id' => $originalPost->forum_thread_id,
            'user_id' => $user->id,
            'body' => 'Réponse',
            'reply_to_post_id' => $originalPost->id,
        ]);

        $this->actingAs($user)
            ->get(route('elus.forum.show', $originalPost->thread))
            ->assertOk()
            ->assertSee('En réponse à')
            ->assertSee('Original Author')
            ->assertSee('Message original');
    }

    public function test_reply_to_post_id_must_be_valid(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('elus.forum.posts.store', $thread), [
                'body' => 'Réponse',
                'reply_to_post_id' => 99999,
            ]);

        $response->assertSessionHasErrors('reply_to_post_id');
    }

    public function test_author_can_detach_reply(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $originalPost = ForumPost::factory()->create();
        $reply = ForumPost::factory()->create([
            'forum_thread_id' => $originalPost->forum_thread_id,
            'user_id' => $user->id,
            'body' => 'Réponse',
            'reply_to_post_id' => $originalPost->id,
        ]);

        $this->assertNotNull($reply->fresh()->reply_to_post_id);

        $this->actingAs($user)
            ->put(route('elus.forum.posts.detach-reply', [$originalPost->thread, $reply]))
            ->assertRedirect(route('elus.forum.show', $originalPost->thread));

        $this->assertNull($reply->fresh()->reply_to_post_id);
    }

    public function test_non_author_cannot_detach_reply(): void
    {
        $author = User::factory()->create(['is_elu' => true]);
        $otherUser = User::factory()->create(['is_elu' => true]);
        $originalPost = ForumPost::factory()->create();
        $reply = ForumPost::factory()->create([
            'forum_thread_id' => $originalPost->forum_thread_id,
            'user_id' => $author->id,
            'body' => 'Réponse',
            'reply_to_post_id' => $originalPost->id,
        ]);

        $this->actingAs($otherUser)
            ->put(route('elus.forum.posts.detach-reply', [$originalPost->thread, $reply]))
            ->assertStatus(403);
    }

    public function test_author_can_edit_own_post(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $post = ForumPost::factory()->create([
            'user_id' => $user->id,
            'body' => 'Message original',
        ]);

        $this->actingAs($user)
            ->put(route('elus.forum.posts.update', [$post->thread, $post]), [
                'body' => 'Message modifié',
            ])
            ->assertRedirect(route('elus.forum.show', $post->thread));

        $this->assertDatabaseHas('forum_posts', [
            'id' => $post->id,
            'body' => 'Message modifié',
        ]);
    }

    public function test_non_author_cannot_edit_post(): void
    {
        $author = User::factory()->create(['is_elu' => true]);
        $other = User::factory()->create(['is_elu' => true]);
        $post = ForumPost::factory()->create([
            'user_id' => $author->id,
        ]);

        $this->actingAs($other)
            ->put(route('elus.forum.posts.update', [$post->thread, $post]), [
                'body' => 'Message pirate',
            ])
            ->assertStatus(403);
    }

    public function test_author_can_delete_own_post(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()->create();
        ForumPost::factory()->create(['forum_thread_id' => $thread->id]); // keep thread alive
        $post = ForumPost::factory()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->delete(route('elus.forum.posts.destroy', [$thread, $post]))
            ->assertRedirect(route('elus.forum.show', $thread));

        $this->assertModelMissing($post);
    }

    public function test_non_author_cannot_delete_post(): void
    {
        $author = User::factory()->create(['is_elu' => true]);
        $other = User::factory()->create(['is_elu' => true]);
        $post = ForumPost::factory()->create([
            'user_id' => $author->id,
        ]);

        $this->actingAs($other)
            ->delete(route('elus.forum.posts.destroy', [$post->thread, $post]))
            ->assertStatus(403);
    }

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create(['is_elu' => true]);
        $thread = ForumThread::factory()->create();
        ForumPost::factory()->create(['forum_thread_id' => $thread->id]); // keep thread alive
        $post = ForumPost::factory()->create([
            'forum_thread_id' => $thread->id,
            'user_id' => $author->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('elus.forum.posts.destroy', [$thread, $post]))
            ->assertRedirect(route('elus.forum.show', $thread));

        $this->assertModelMissing($post);
    }

    public function test_deleting_last_post_deletes_thread(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $post = ForumPost::factory()->create([
            'user_id' => $user->id,
        ]);
        $threadId = $post->thread->id;

        $this->actingAs($user)
            ->delete(route('elus.forum.posts.destroy', [$post->thread, $post]))
            ->assertRedirect(route('elus.forum.index'));

        $this->assertDatabaseMissing('forum_posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('forum_threads', ['id' => $threadId]);
    }
}
