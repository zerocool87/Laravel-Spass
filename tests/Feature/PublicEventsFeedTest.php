<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicEventsFeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function events_json_is_accessible_without_auth()
    {
        // Create upcoming events
        Event::factory()->count(3)->create([
            'start_at' => now()->addDays(1),
        ]);

        $response = $this->getJson('/events/json');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                ['id', 'title', 'start', 'end', 'allDay', 'type', 'url'],
            ]);
    }
}
