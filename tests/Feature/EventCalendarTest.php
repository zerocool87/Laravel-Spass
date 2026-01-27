<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_json_feed_returns_events()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'title' => 'Cal Event',
            'start_at' => now()->addDays(1),
            'end_at' => now()->addDays(1)->addHour(),
            'created_by' => $user->id,
        ]);

        $start = now()->startOfWeek()->toIso8601String();
        $end = now()->endOfWeek()->toIso8601String();

        $response = $this->actingAs($user)->getJson(route('events.json', ['start' => $start, 'end' => $end]));

        $response->assertStatus(200);
        $response->assertJsonStructure([['id', 'title', 'start', 'end', 'allDay', 'url']]);
        $this->assertStringContainsString('Cal Event', $response->getContent());
    }

    public function test_calendar_view_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('events.calendar'));
        $response->assertStatus(200);
        $response->assertSee('Calendar');
        $response->assertSee('calendar'); // the div id

        // events index should also include an embedded calendar
        $response = $this->actingAs($user)->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('<div id="events-calendar"', false);
    }
}
