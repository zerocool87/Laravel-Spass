<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+14 days');
        $end = (clone $start)->modify('+1 hour');

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'start_at' => $start,
            'end_at' => $end,
            'location' => $this->faker->city(),
            'is_all_day' => false,
            'type' => $this->faker->randomElement(config('events.types', ['Autre'])),
            'created_by' => User::factory(),
        ];
    }
}
