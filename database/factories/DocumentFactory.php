<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'path' => 'documents/'.$this->faker->uuid().'.pdf',
            'original_name' => $this->faker->word().'.pdf',
            'created_by' => User::factory(),
            'visible_to_all' => $this->faker->boolean(70),
            'titres' => null,
            'category' => $this->faker->optional()->randomElement(
                config('documents.categories', []),
            ),
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible_to_all' => true,
            'titres' => null,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visible_to_all' => false,
            'titres' => [],
        ]);
    }
}
