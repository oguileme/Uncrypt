<?php

namespace Database\Seeders;

use App\Models\TypeEncrypton;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeEncryptionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Tipos de cifra disponiveis - os nomes precisam bater com o encryptByTypeName do CipherHelper.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Cifra de Cesar',
                'description' => 'Julius Caesar deslocava cada letra do alfabeto por um numero fixo para proteger suas mensagens militares. Voce precisa descobrir o deslocamento usado.',
                'difficulty' => 'easy',
            ],
            [
                'name' => 'ROT13',
                'description' => 'Uma cifra de substituicao simples: cada letra e substituida pela letra 13 posicoes a frente no alfabeto. Aplicar duas vezes volta ao original.',
                'difficulty' => 'easy',
            ],
            [
                'name' => 'Base64',
                'description' => 'Tecnicamente nao e criptografia - e uma codificacao que transforma dados binarios em texto ASCII. Pode ser revertida sem nenhuma chave.',
                'difficulty' => 'medium',
            ],
            [
                'name' => 'Atbash',
                'description' => 'Cada letra e substituida pela sua simetrica no alfabeto: A vira Z, B vira Y, e assim por diante. Nao precisa de chave.',
                'difficulty' => 'easy',
            ],
            [
                'name' => 'Morse',
                'description' => 'Sistema de telegrafia baseado em pontos e tracos. Cada letra do alfabeto e representada por uma unica sequencia de sinais.',
                'difficulty' => 'medium',
            ],
            [
                'name' => 'Vigenère',
                'description' => 'Cada letra do texto e deslocada pela quantidade indicada pela letra correspondente de uma palavra-chave repetida. Quanto mais longa a chave, mais segura a cifra.',
                'difficulty' => 'hard',
            ],
        ];

        foreach ($types as $type) {
            TypeEncrypton::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
