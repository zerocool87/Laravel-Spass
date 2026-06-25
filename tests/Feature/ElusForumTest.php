<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElusForumTest extends TestCase
{
    use RefreshDatabase;

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
        $instance = Instance::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('elus.forum.store'), [
                'instance_id' => $instance->id,
                'title' => 'Sujet test',
                'body' => 'Premier message du sujet',
            ]);

        $thread = ForumThread::first();

        $this->assertNotNull($thread);
        $this->assertEquals('Sujet test', $thread->title);
        $this->assertEquals($instance->id, $thread->instance_id);
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
        $instance = Instance::factory()->create();

        $this->actingAs($user)
            ->post(route('elus.forum.store'), [
                'instance_id' => $instance->id,
                'title' => 'Test',
                'body' => 'Test',
            ])
            ->assertStatus(403);
    }

    public function test_threads_listed_in_table_on_index(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instanceA = Instance::factory()->create(['name' => 'Commission A']);
        $instanceB = Instance::factory()->create(['name' => 'Commission B']);

        ForumThread::factory()->create(['instance_id' => $instanceA->id, 'title' => 'Sujet A']);
        ForumThread::factory()->create(['instance_id' => $instanceB->id, 'title' => 'Sujet B']);

        $this->actingAs($user)
            ->get(route('elus.forum.index'))
            ->assertOk()
            ->assertSee('Commission A')
            ->assertSee('Commission B')
            ->assertSee('Sujet A')
            ->assertSee('Sujet B')
            ->assertSeeInOrder([__('Instance'), __('Sujet'), __('Auteur'), __('Rép.'), __('Dernière activité')]);
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

    public function test_index_paginates_at_30(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instance = Instance::factory()->create();

        ForumThread::factory()
            ->count(31)
            ->create(['instance_id' => $instance->id]);

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
        $instance = Instance::factory()->create();

        ForumThread::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Budget participatif 2026',
        ]);
        ForumThread::factory()->create([
            'instance_id' => $instance->id,
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
        $instanceA = Instance::factory()->create(['name' => 'Commission Eau']);
        $instanceB = Instance::factory()->create(['name' => 'Commission Voirie']);

        ForumThread::factory()->create([
            'instance_id' => $instanceA->id,
            'title' => 'Sujet Eau',
        ]);
        ForumThread::factory()->create([
            'instance_id' => $instanceB->id,
            'title' => 'Sujet Voirie',
        ]);

        $this->actingAs($user)
            ->get(route('elus.forum.index', ['instance_id' => $instanceA->id]))
            ->assertOk()
            ->assertSee('Sujet Eau')
            ->assertDontSee('Sujet Voirie');
    }

    public function test_index_sort_by_replies(): void
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instance = Instance::factory()->create();

        $threadMany = ForumThread::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Très actif',
        ]);
        ForumPost::factory()->count(5)->create([
            'forum_thread_id' => $threadMany->id,
        ]);

        $threadFew = ForumThread::factory()->create([
            'instance_id' => $instance->id,
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

}
