<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Conquistas do arsenal criptografico.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Primeira Chave',
                'description' => 'Decifre sua primeira mensagem e absorva a essência da criptografia clássica.',
                'xp_reward' => 50,
                'required_count' => 1,
                'icon' => 'key',
                'color' => 'green',
            ],
            [
                'name' => 'Rotor em Movimento',
                'description' => 'Complete 5 desafios usando qualquer cifra para colocar o rotor para girar.',
                'xp_reward' => 100,
                'required_count' => 5,
                'icon' => 'rotor',
                'color' => 'blue',
            ],
            [
                'name' => 'Engenho de Precisão',
                'description' => 'Acerte 10 mensagens seguidas e afine o mecanismo do seu arsenal.',
                'xp_reward' => 150,
                'required_count' => 10,
                'icon' => 'gear',
                'color' => 'orange',
            ],
            [
                'name' => 'Transmissor de Morse',
                'description' => 'Decifre mensagens em código Morse e sinalize sua habilidade no ar.',
                'xp_reward' => 120,
                'required_count' => 3,
                'icon' => 'wave',
                'color' => 'yellow',
            ],
            [
                'name' => 'Cofre Intacto',
                'description' => 'Resolva um desafio sem recorrer a nenhuma dica e mantenha o cofre lacrado.',
                'xp_reward' => 80,
                'required_count' => 1,
                'icon' => 'lock',
                'color' => 'green',
            ],
            [
                'name' => 'Estrela do Criptógrafo',
                'description' => 'Acumule 500 XP e conquiste sua estrela no alto do arsenal.',
                'xp_reward' => 200,
                'required_count' => 500,
                'icon' => 'star',
                'color' => 'purple',
            ],
            [
                'name' => 'Colecionador de Cifras',
                'description' => 'Domine ao menos 4 tipos diferentes de cifra na sua coleção.',
                'xp_reward' => 250,
                'required_count' => 4,
                'icon' => 'vault',
                'color' => 'blue',
            ],
            [
                'name' => 'Guardião do Arquivo',
                'description' => 'Complete todos os desafios disponíveis e vire o guardião do arquivo secreto.',
                'xp_reward' => 500,
                'required_count' => 25,
                'icon' => 'compass',
                'color' => 'yellow',
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['name' => $achievement['name']],
                [
                    'description' => $achievement['description'],
                    'xp_reward' => $achievement['xp_reward'],
                    'required_count' => $achievement['required_count'],
                    'icon' => $achievement['icon'],
                    'color' => $achievement['color'],
                ]
            );
        }
    }
}
