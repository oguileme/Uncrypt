<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'xp_reward' => fake()->numberBetween(10, 100),
            'required_count' => fake()->numberBetween(1, 10),
            'icon' => fake()->randomElement(['key', 'rotor', 'gear', 'wave', 'lock', 'star', 'vault', 'compass']),
            'color' => fake()->randomElement(['green', 'blue', 'yellow', 'purple', 'orange']),
        ];
    }
}
