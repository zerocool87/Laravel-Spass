<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Thematique;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumThread>
 */
class ForumThreadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thematique_id' => Thematique::factory(),
            'title' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'is_pinned' => false,
        ];
    }
}
