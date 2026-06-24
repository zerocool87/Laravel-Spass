<?php

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

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code_insee' => fake()->numerify('87###'),
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => fake()->city(),
            'titre' => 'Conseiller municipal',
        ];
    }
}
