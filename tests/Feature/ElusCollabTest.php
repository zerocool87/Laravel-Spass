<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElusCollabTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_elu_cannot_access_collab()
    {
        $user = User::factory()->create(['is_elu' => false, 'is_admin' => false]);

        $this->actingAs($user)
            ->get(route('elus.collab.index'))
            ->assertStatus(403);
    }

    public function test_elu_can_start_conversation_and_send_message()
    {
        $sender = User::factory()->create(['is_elu' => true]);
        $recipient = User::factory()->create(['is_elu' => true]);

        $response = $this->actingAs($sender)
            ->post(route('elus.collab.store'), ['recipient_id' => $recipient->id]);

        $response->assertRedirect();

        $conversation = Conversation::first();

        $this->assertNotNull($conversation);
        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);
        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $conversation->id,
            'user_id' => $recipient->id,
        ]);

        $this->actingAs($sender)
            ->post(route('elus.collab.messages.store', $conversation), ['body' => 'Bonjour'])
            ->assertRedirect(route('elus.collab.show', $conversation));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
            'body' => 'Bonjour',
        ]);
    }

    public function test_non_participant_cannot_view_conversation()
    {
        $userA = User::factory()->create(['is_elu' => true]);
        $userB = User::factory()->create(['is_elu' => true]);
        $intruder = User::factory()->create(['is_elu' => true]);

        $conversation = Conversation::create(['created_by' => $userA->id]);
        $conversation->users()->attach([$userA->id, $userB->id]);

        $this->actingAs($intruder)
            ->get(route('elus.collab.show', $conversation))
            ->assertStatus(403);
    }
}
