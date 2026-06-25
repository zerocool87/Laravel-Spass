<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(array_column(ProjectType::cases(), 'value')),
            'status' => $this->faker->randomElement(array_column(ProjectStatus::cases(), 'value')),
            'commune' => 'Limoges',
            'budget' => $this->faker->randomFloat(2, 1000, 500000),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+1 year'),
            'territories' => [],
            'indicators' => [],
        ];
    }
}
