<?php

namespace App\Helpers;

class CipherHelper
{
    public static function CesarEncrypt(String $text, String $shift){

        $result = "";
        foreach(str_split(strtoupper($text)) as $char){
            if(ctype_alpha($char)){
                // pega a posição do caractere na tabela ASCII e subtrai 65 para obter a posição no alfabeto (A=0, B=1, ..., Z=25)
                $position = ord($char) - 65;
                // aplica o deslocamento e usa o operador módulo para garantir que a posição fique dentro do intervalo de 0 a 25
                $newPosition = ($position + $shift) % 26;
                //faz o caminho inverso para obter o caractere correspondente à nova posição e adiciona ao resultado
                $result .= chr($newPosition + 65);
            }else{
                $result .= $char;
            }
        }
        return $result;
    }

    public static function CesarDecrypt(String $text, String $shift){
        // para descriptografar, basta chamar a função de criptografia com o deslocamento negativo
        return self::CesarEncrypt($text, -$shift);
    }
}