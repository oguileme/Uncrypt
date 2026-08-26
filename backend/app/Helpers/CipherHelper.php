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

    public static function ROT13Encrypt(String $text){
        return self::CesarEncrypt($text, 13);
    }

    public static function ROT13Decrypt(String $text){
        return self::CesarDecrypt($text, -13);
    }

    public static function base64Encrypt(String $text){
        return base64_encode($text);
    }

    public static function base64Decrypt(String $text){
        return base64_decode($text);
    }

    public static function atbashEncrypt(String $text){
        return strtr($text, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', 'ZYXWVUTSRQPONMLKJIHGFEDCBAzyxwvutsrqponmlkjihgfedcba');
    }
    
    public static function atbashDecrypt(String $text){
        return self::atbashEncrypt($text);
    }

    public static function morseEncrypt(String $text){
        $morseCode = [
            'A' => '.-', 'B' => '-...', 'C' => '-.-.', 'D' => '-..', 'E' => '.', 'F' => '..-.', 'G' => '--.', 'H' => '....', 'I' => '..', 'J' => '.---', 'K' => '-.-', 'L' => '.-..', 'M' => '--', 'N' => '-.', 'O' => '---', 'P' => '.--.', 'Q' => '--.-', 'R' => '.-.', 'S' => '...', 'T' => '-', 'U' => '..-', 'V' => '...-', 'W' => '.--', 'X' => '-..-', 'Y' => '-.--', 'Z' => '--..',
            '0' => '-----', '1' => '.----', '2' => '..---', '3' => '...--', '4' => '....-', '5' => '.....', '6' => '-....', '7' => '--...', '8' => '---..', '9' => '----.',
            '.' => '.-.-.-', ',' => '--..--', '?' => '..--..', '\''=>'.----.','!'=>'-.-.--','/'=>'-..-.','('=>'-.--.','&'=>'.-...','='=>'-...-','+'=>'.-.-.','-'=>'-....-','_'=>'..--.-','"'=>'.-..-.','$'=>'...-..-','@'=>'.--.-.'
        ];
        $result = '';
        foreach(str_split(strtoupper($text)) as $char){
            if(isset($morseCode[$char])){
                $result .= $morseCode[$char] . " ";
            }else{
                $result .= " ";
            }
        }
        return trim($result);
    }

    public static function morseDecrypt(String $text){
        $morseCode = [
            '.-' => 'A', '-...' => 'B', '-.-.' => 'C', '-..' => 'D', '.' => 'E', '..-.' => 'F', '--.' => 'G', '....' => 'H', '..' => 'I', '.---' => 'J', '-.-' => 'K', '.-..' => 'L', '--' => 'M', '-.' => 'N', '---' => 'O', '.--.' => 'P', '--.-' => 'Q', '.-.' => 'R', '...' => 'S', '-' => 'T', '..-' => 'U', '...-' => 'V', '.--' => 'W', '-..-' => 'X', '-.--' => 'Y', '--..' => 'Z',
            '-----' => '0', '.----' => '1', '..---' => '2', '...--' => '3', '....-' => '4', '.....' => '5', '-....' => '6', '--...' => '7', '---..' => '8', '----.' => '9',
            '.-.-.-'=>'.','--..--'=>',','..--..'=>'?','.----.'=>'\'','-.-.--'=>'!','-..-.'=>'/','-.--.'=>'(','-.--.-'=>')','.-...'=>'&','---...'=>':','-.-.-.'=>';','-...-'=>'=','.-.-.'=>'+','-....-'=>'-','..--.-'=>'_','.-..-.'=>'"','...-..-'=>'$','.--.-.'=>'@'
        ];
        $result = '';
        foreach(explode(' ', $text) as $code){
            if(isset($morseCode[$code])){
                $result .= $morseCode[$code];
            }else{
                $result .= " ";
            }
        }
        return trim($result);
    }

    // aplica a cifra correspondente ao tipo de criptografia cadastrado no banco
    public static function encryptByTypeName(String $typeName, String $text, ?String $key = null): String{
        return match($typeName){
            'Cifra de Cesar' => self::CesarEncrypt($text, $key ?? '3'),
            'ROT13' => self::ROT13Encrypt($text),
            'Base64' => self::base64Encrypt($text),
            default => $text,
        };
    }
}