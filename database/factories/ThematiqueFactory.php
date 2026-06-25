<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Thematique;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thematique>
 */
class ThematiqueFactory extends Factory
{
    protected $model = Thematique::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
