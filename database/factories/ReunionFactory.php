<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReunionStatus;
use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reunion>
 */
class ReunionFactory extends Factory
{
    protected $model = Reunion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('now', '+1 month');
        $startTime = $date->format('Y-m-d H:i:s');
        $endTime = $date->modify('+2 hours')->format('Y-m-d H:i:s');

        return [
            'instance_id' => Instance::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $this->faker->address(),
            'status' => $this->faker->randomElement(array_column(ReunionStatus::cases(), 'value')),
            'participants' => [],
            'ordre_du_jour' => $this->faker->paragraph(),
            'compte_rendu' => $this->faker->paragraph(),
            'documents' => [],
        ];
    }
}
