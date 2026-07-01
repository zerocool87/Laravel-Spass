<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ReunionStatus;
use App\Models\Instance;
use App\Models\Reunion;
use App\Services\ReunionService;
use Carbon\Carbon;
use Tests\TestCase;

class ReunionServiceTest extends TestCase
{
    private ReunionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ReunionService::class);
    }

    public function test_find_conflicts_returns_overlapping_reunion(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $conflicts = $this->service->findConflicts($instance->id, $start->copy()->addHour(), $start->copy()->addHours(1.5));

        $this->assertCount(1, $conflicts);
        $this->assertEquals($instance->id, $conflicts->first()->instance_id);
    }

    public function test_find_conflicts_returns_empty_when_no_overlap(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $conflicts = $this->service->findConflicts($instance->id, $start->copy()->addHours(3), $start->copy()->addHours(5));

        $this->assertCount(0, $conflicts);
    }

    public function test_find_conflicts_excludes_given_id(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        $reunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $conflicts = $this->service->findConflicts($instance->id, $start, $end, $reunion->id);

        $this->assertCount(0, $conflicts);
    }

    public function test_find_conflicts_ignores_cancelled_reunions(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Annulee->value,
        ]);

        $conflicts = $this->service->findConflicts($instance->id, $start, $end);

        $this->assertCount(0, $conflicts);
    }

    public function test_has_conflicts_returns_true_when_overlap_exists(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $this->assertTrue($this->service->hasConflicts($instance->id, $start, $end));
    }

    public function test_has_conflicts_returns_false_when_no_overlap(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');

        $this->assertFalse($this->service->hasConflicts($instance->id, $start, $start->copy()->addHour()));
    }

    public function test_suggest_slots_returns_available_time_slots(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $slots = $this->service->suggestSlots($instance->id, $start, $end);

        $this->assertNotEmpty($slots);
        $this->assertCount(3, $slots);
        $this->assertArrayHasKey('start', $slots[0]);
        $this->assertArrayHasKey('end', $slots[0]);
    }

    public function test_find_conflicts_scoped_to_instance(): void
    {
        $instanceA = Instance::factory()->create();
        $instanceB = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 12:00:00');

        Reunion::factory()->create([
            'instance_id' => $instanceA->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $conflicts = $this->service->findConflicts($instanceB->id, $start, $end);

        $this->assertCount(0, $conflicts);
    }

    public function test_adjacent_reunions_do_not_conflict(): void
    {
        $instance = Instance::factory()->create();
        $start = Carbon::parse('2026-07-15 10:00:00');
        $end = Carbon::parse('2026-07-15 11:00:00');

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => ReunionStatus::Planifiee->value,
        ]);

        $conflicts = $this->service->findConflicts($instance->id, $end, $end->copy()->addHour());

        $this->assertCount(0, $conflicts);
    }
}
