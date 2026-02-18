<?php

namespace Database\Factories;

use App\Models\Reunion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReunionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reunion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('now', '+1 month');
        $startTime = $date->format('Y-m-d H:i:s');
        $endTime = $date->modify('+2 hours')->format('Y-m-d H:i:s');

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $this->faker->address,
            'status' => $this->faker->randomElement(array_keys(Reunion::STATUSES)),
            'participants' => [],
            'ordre_du_jour' => $this->faker->paragraph,
            'compte_rendu' => $this->faker->paragraph,
            'documents' => [],
        ];
    }
}
