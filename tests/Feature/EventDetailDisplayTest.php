<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventDetailDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set locale for Carbon and date formatting for the tests
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        Carbon::setLocale('fr');
    }

    /**
     * Helper to render the event detail view component with a given event configuration.
     *
     * @param array $overrides Attributes for the event object.
     * @return \Illuminate\Testing\TestView
     */
    private function renderDetailView(array $overrides = [])
    {
        $event = new \stdClass();
        $event->title = 'Test Event';
        $event->description = 'Test Description';
        $event->location = 'Test Location';
        $event->type = 'Test Type';

        foreach ($overrides as $key => $value) {
            $event->{$key} = $value;
        }

        return $this->view('events._detail', ['event' => $event]);
    }

    public function test_single_day_timed_event_without_end_time_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-14 09:00:00', 'UTC'),
            'end_at' => null,
            'is_all_day' => false,
        ]);

        $view->assertSee('mardi 14 février 2023', false);
        $view->assertSee('·');
        $view->assertSee('09:00', false);
        $view->assertDontSee('–');
        $view->assertDontSee('Toute la journée');
        $view->assertDontSee('(UTC');
    }

    public function test_single_day_timed_range_event_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-07 14:00:00', 'UTC'),
            'end_at' => Carbon::parse('2023-02-07 16:30:00', 'UTC'),
            'is_all_day' => false,
        ]);

        $view->assertSee('mardi 7 février 2023', false);
        $view->assertSee('14:00–16:30', false);
        $view->assertDontSee('Toute la journée');
    }

    public function test_single_all_day_event_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-08 00:00:00', 'UTC'),
            'end_at' => null,
            'is_all_day' => true,
        ]);

        $view->assertSee('mercredi 8 février 2023', false);
        $view->assertSee('Toute la journée', false);
    }

    public function test_multi_day_all_day_event_in_same_month_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-07 00:00:00', 'UTC'),
            'end_at' => Carbon::parse('2023-02-10 00:00:00', 'UTC'), // Exclusive end date
            'is_all_day' => true,
        ]);

        $view->assertSee('7–9 février 2023', false);
        $view->assertSee('Toute la journée', false);
    }

    public function test_multi_day_all_day_event_over_different_months_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-27 00:00:00', 'UTC'),
            'end_at' => Carbon::parse('2023-03-03 00:00:00', 'UTC'), // Exclusive end date
            'is_all_day' => true,
        ]);

        $view->assertSee('27 février–2 mars 2023', false);
        $view->assertSee('Toute la journée', false);
    }
    
    public function test_multi_day_all_day_event_over_different_years_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-12-30 00:00:00', 'UTC'),
            'end_at' => Carbon::parse('2024-01-03 00:00:00', 'UTC'), // Exclusive end date
            'is_all_day' => true,
        ]);

        $view->assertSee('30 décembre 2023–2 janvier 2024', false);
        $view->assertSee('Toute la journée', false);
    }

    public function test_multi_day_timed_event_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-04-10 20:00:00', 'UTC'),
            'end_at' => Carbon::parse('2023-04-12 11:00:00', 'UTC'),
            'is_all_day' => false,
        ]);

        $view->assertSee('Du 10 avr. 2023 à 20:00 au 12 avr. 2023 à 11:00', false);
    }

    public function test_timed_event_with_timezone_displays_correctly()
    {
        $view = $this->renderDetailView([
            'start_at' => Carbon::parse('2023-02-07 14:00:00', 'Europe/Paris'), // UTC+1 in winter
            'end_at' => Carbon::parse('2023-02-07 16:30:00', 'Europe/Paris'),
            'is_all_day' => false,
        ]);

        $view->assertSee('mardi 7 février 2023', false);
        $view->assertSee('14:00–16:30', false);
        $view->assertSee('(UTC+1)', false);
    }
}