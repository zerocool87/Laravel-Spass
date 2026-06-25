<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EluProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EluProfile>
 */
class EluProfileFactory extends Factory
{
    protected $model = EluProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code_insee' => $this->faker->numerify('#####'),
            'civilite' => $this->faker->randomElement(['M.', 'Mme']),
            'telephone' => $this->faker->phoneNumber(),
            'profession' => $this->faker->jobTitle(),
            'date_naissance' => $this->faker->date(),
        ];
    }
}
