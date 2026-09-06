<?php

namespace Database\Factories;

use App\Models\TypeEncrypton;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TypeEncrypton>
 */
class TypeEncryptonFactory extends Factory
{
    protected $model = TypeEncrypton::class;

    public function definition(): array
    {
        $names = ['Cifra de Cesar', 'ROT13', 'Base64', 'Atbash', 'Morse', 'Vigenère'];

        return [
            'name' => fake()->randomElement($names),
            'description' => fake()->sentence(),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}