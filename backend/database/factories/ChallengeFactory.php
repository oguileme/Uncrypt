<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\TypeEncrypton;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'type_encryption_id' => TypeEncrypton::factory(),
            'phrase' => 'mensagem secreta',
            'key' => '3',
            'xp' => 10,
            'is_active' => true,
            'hint' => fake()->sentence(),
        ];
    }
}