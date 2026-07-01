<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ReunionStatus;
use App\Models\Reunion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReunionService
{
    /**
     * Find reunions overlapping the given time window for an instance.
     *
     * @return Collection<int, Reunion>
     */
    public function findConflicts(int $instanceId, Carbon $start, Carbon $end, ?int $excludeId = null): Collection
    {
        $start = $start->copy()->setTimezone('UTC');
        $end = $end->copy()->setTimezone('UTC');

        return Reunion::where('instance_id', $instanceId)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->whereIn('status', [ReunionStatus::Planifiee->value, ReunionStatus::Confirmee->value])
            ->get();
    }

    /**
     * Check if a specific time window has conflicts.
     */
    public function hasConflicts(int $instanceId, Carbon $start, Carbon $end, ?int $excludeId = null): bool
    {
        return $this->findConflicts($instanceId, $start, $end, $excludeId)->isNotEmpty();
    }

    /**
     * Suggest up to three free time slots by advancing the requested window by 2h steps.
     *
     * @return list<array{start: string, end: string}>
     */
    public function suggestSlots(int $instanceId, Carbon $start, Carbon $end): array
    {
        $duration = (int) $end->diffInMinutes($start);
        $alternatives = [];
        $current = $start->copy();
        $allConflicts = $this->loadAllPotentialConflicts($instanceId, $start, $duration);

        for ($i = 0; $i < 10; $i++) {
            $current->addHours(2);
            $proposedEnd = $current->copy()->addMinutes($duration);

            if (! $this->hasOverlapWithConflicts($allConflicts, $current, $proposedEnd)) {
                $alternatives[] = [
                    'start' => $current->format('H:i'),
                    'end' => $proposedEnd->format('H:i'),
                ];

                if (count($alternatives) >= 3) {
                    break;
                }
            }
        }

        return $alternatives;
    }

    /**
     * Load all potential conflicts for the day in a single query.
     *
     * @return Collection<int, Reunion>
     */
    private function loadAllPotentialConflicts(int $instanceId, Carbon $start, int $durationMinutes): Collection
    {
        $dayStart = $start->copy()->startOfDay();
        $dayEnd = $start->copy()->endOfDay();

        return Reunion::where('instance_id', $instanceId)
            ->where('start_time', '>=', $dayStart)
            ->where('start_time', '<=', $dayEnd)
            ->whereIn('status', [ReunionStatus::Planifiee->value, ReunionStatus::Confirmee->value])
            ->get();
    }

    /** @param Collection<int, Reunion> $conflicts */
    private function hasOverlapWithConflicts(Collection $conflicts, Carbon $start, Carbon $end): bool
    {
        foreach ($conflicts as $reunion) {
            if ($reunion->start_time < $end && $reunion->end_time > $start) {
                return true;
            }
        }

        return false;
    }
}
