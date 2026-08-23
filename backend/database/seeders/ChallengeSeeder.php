<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\TypeEncrypton;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * 5 desafios para cada tipo de cifra cadastrado.
     * Frases sem acento e apenas com letras/espacos (limitacao das cifras classicas).
     */
    public function run(): void
    {
        $challenges = [
            // ===== CIFRA DE CESAR =====
            [
                'type' => 'Cifra de Cesar',
                'title' => 'A Primeira Mensagem',
                'description' => 'Julius Caesar usava esse metodo para proteger suas mensagens militares. Cada letra e deslocada por um numero fixo no alfabeto.',
                'phrase' => 'encontre o tesouro escondido',
                'key' => '3',
                'xp' => 10,
                'hint' => 'O deslocamento mais famoso da historia: a letra D esconde o A.',
            ],
            [
                'type' => 'Cifra de Cesar',
                'title' => 'Mensagem de Guerra',
                'description' => 'Um general interceptou uma mensagem inimiga cifrada. Cada letra foi empurrada sete casas adiante no alfabeto.',
                'phrase' => 'ataque ao amanhecer',
                'key' => '7',
                'xp' => 15,
                'hint' => 'Volte sete casas no alfabeto para revelar cada letra.',
            ],
            [
                'type' => 'Cifra de Cesar',
                'title' => 'Carta Cifrada',
                'description' => 'Uma carta antiga chegou as suas maos. O remetente escolheu um numero primo maior que dez como deslocamento.',
                'phrase' => 'o passaro voa ao entardecer',
                'key' => '11',
                'xp' => 20,
                'hint' => 'O deslocamento e um numero primo entre 10 e 15.',
            ],
            [
                'type' => 'Cifra de Cesar',
                'title' => 'Codigo do Imperio',
                'description' => 'As ordens reais eram enviadas cifradas aos governadores. Descubra o que essa mensagem ordena.',
                'phrase' => 'a coroa pertence ao rei',
                'key' => '5',
                'xp' => 25,
                'hint' => 'Cinco posicoes separam cada letra cifrada da letra verdadeira.',
            ],
            [
                'type' => 'Cifra de Cesar',
                'title' => 'O Grande Salto',
                'description' => 'Desta vez o deslocamento foi grande: mais da metade do alfabeto. Um bom ataque de frequencia resolve.',
                'phrase' => 'a chave esta no espelho',
                'key' => '17',
                'xp' => 30,
                'hint' => 'Mais da metade do alfabeto: o deslocamento e maior que 13 e menor que 26.',
            ],

            // ===== ROT13 =====
            [
                'type' => 'ROT13',
                'title' => 'A Porta de Entrada',
                'description' => 'ROT13 e uma cifra de substituicao simples. Cada letra e substituida pela letra 13 posicoes a frente no alfabeto.',
                'phrase' => 'criptografia e arte',
                'key' => '-',
                'xp' => 10,
                'hint' => 'METAA METAA. A metade do alfabeto e a resposta.',
            ],
            [
                'type' => 'ROT13',
                'title' => 'Espelho do Alfabeto',
                'description' => 'Treze posicoes separam cada letra de seu segredo. O melhor da ROT13: cifrar e decifrar sao o mesmo movimento.',
                'phrase' => 'a resposta esta no meio',
                'key' => '-',
                'xp' => 15,
                'hint' => 'Avance treze letras - ou volte treze, o resultado e o mesmo.',
            ],
            [
                'type' => 'ROT13',
                'title' => 'Simples Demais',
                'description' => 'Se aplicar a mesma operacao duas vezes retorna ao texto original, que cifra seria essa?',
                'phrase' => 'sempre foi simples demais',
                'key' => '-',
                'xp' => 20,
                'hint' => 'Duas aplicacoes cancelam uma a outra. Pense em metades do alfabeto.',
            ],
            [
                'type' => 'ROT13',
                'title' => 'O Classico Romano',
                'description' => 'Uma variacao fixa da cifra de Caesar que virou padrao em foruns da internet para esconder spoilers.',
                'phrase' => 'julius aprovaria isso',
                'key' => '-',
                'xp' => 25,
                'hint' => 'E a cifra de Caesar com deslocamento fixo de treze.',
            ],
            [
                'type' => 'ROT13',
                'title' => 'Treze Passos',
                'description' => 'Conte vinte e seis letras do alfabeto e divida ao meio. A resposta mora nessa fronteira.',
                'phrase' => 'treze e a metade de vinte seis',
                'key' => '-',
                'xp' => 30,
                'hint' => 'O proprio nome entrega o deslocamento: R-O-T e T-R-E-Z-E invertido.',
            ],

            // ===== BASE64 =====
            [
                'type' => 'Base64',
                'title' => 'O Impostor',
                'description' => 'Base64 nao e criptografia de verdade - e uma codificacao. Transforma dados binarios em texto ASCII e pode ser revertida sem chave.',
                'phrase' => 'descifrar',
                'key' => '-',
                'xp' => 10,
                'hint' => 'Isso nao e criptografia de verdade - procure uma ferramenta online de Base64 decode.',
            ],
            [
                'type' => 'Base64',
                'title' => 'Seguranca Falsa',
                'description' => 'Muitos confundem codificacao com criptografia. Decodifique e descubra por que Base64 nao protege nada.',
                'phrase' => 'isto nao e segredo',
                'key' => '-',
                'xp' => 15,
                'hint' => 'Strings Base64 usam apenas A-Z, a-z, 0-9, + e /.',
            ],
            [
                'type' => 'Base64',
                'title' => 'Binario Vestido de Texto',
                'description' => 'Por baixo do capo, cada grupo de 4 caracteres Base64 representa 3 bytes do texto original.',
                'phrase' => 'quatro letras tres bytes',
                'key' => '-',
                'xp' => 20,
                'hint' => 'A proporcao 4:3 entre caracteres e bytes e a assinatura dessa codificacao.',
            ],
            [
                'type' => 'Base64',
                'title' => 'O Alfabeto de 64',
                'description' => 'O nome entrega tudo: um alfabeto com exatamente sessenta e quatro simbolos. Qual mensagem ele esconde?',
                'phrase' => 'sessenta e quatro simbolos',
                'key' => '-',
                'xp' => 25,
                'hint' => 'Conte os simbolos possiveis: 26 maiusculas + 26 minusculas + 10 digitos + 2 especiais.',
            ],
            [
                'type' => 'Base64',
                'title' => 'Igual no Final',
                'description' => 'Os sinais de igual no fim de uma string Base64 nao sao decoracao: eles completam o ultimo bloco.',
                'phrase' => 'preste atencao nos iguais',
                'key' => '-',
                'xp' => 30,
                'hint' => 'Os = no final indicam padding. Quantidade impares de bytes geram um ou dois iguais.',
            ],
        ];

        foreach ($challenges as $challenge) {
            $type = TypeEncrypton::where('name', $challenge['type'])->firstOrFail();

            Challenge::updateOrCreate(
                ['title' => $challenge['title']],
                [
                    'description' => $challenge['description'],
                    'type_encryption_id' => $type->id,
                    'phrase' => $challenge['phrase'],
                    'key' => $challenge['key'],
                    'xp' => $challenge['xp'],
                    'is_active' => true,
                    'hint' => $challenge['hint'],
                ]
            );
        }
    }
}
