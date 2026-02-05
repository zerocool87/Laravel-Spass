<?php

namespace Database\Factories;

use App\Models\Instance;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'type' => $this->faker->randomElement(array_keys(Instance::TYPES)),
            'description' => $this->faker->paragraph,
            'territory' => $this->faker->city,
            'members' => [],
        ];
    }
}