<?php



namespace App\Helpers;

class MiscHelper
{


    // public static function strHasData($value){

    //     if($value == array()){
    //         $value = "";
    //     }

    //     if(is_array($value)){
    //         return true;
    //     }

    //     if(is_object($value)){
    //         return true;
    //     }

    //     if(isset($value) && _strlen($value) > 0){
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public static function arrayHasData($value){

    //     if(is_array($value) && sizeof($value) > 0){
    //         return true;
    //     } else {
    //         return false;
    //     }

    // }

    public static function eJson($value, $is_for_js = false)
    {

        $value = _str_replace("\\", "\\\\", $value);
        $value = _str_replace("\"", "\\\"", $value);
        $value = _str_replace("\t", "\\t", $value);
        $value = preg_replace('/\r?\n|\r/', '\\n', $value);

        //This should come at the end
        $value = _str_replace("_x000D_", "\\n", $value);

        if ($is_for_js == true) {
            $value = _str_replace("'", "\'", $value);
        }


        return $value;
    }

    public static function pluralize($word, $number)
    {

        if ($number != 1) {
            $word .= "s";
        }

        return $word;
    }

    public static function removeNewline($string)
    {

        $string = _str_replace("\r\n", "", $string);
        $string = _str_replace("\n", "", $string);

        return $string;
    }


    public static function numToAlphabet($n)
    {

        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n % 26 + 0x41) . $r;
        return $r;
    }

    public static function onlyAlphabetNumbers($string)
    {
        $string = preg_replace('/[^A-Za-z0-9]/', '', $string);
        return $string;
    }

    public static function randomString($length = 10)
    {
        $bytes = openssl_random_pseudo_bytes($length);
        $hex   = bin2hex($bytes);

        return $hex;
    }

    public static function randomStrongPassword()
    {

        $digits    = array_flip(range('0', '9'));
        $lowercase = array_flip(range('a', 'z'));
        $uppercase = array_flip(range('A', 'Z'));
        $special   = array_flip(str_split('!@#$%^&*()_+=-}{[}]\|<>?/'));
        $combined  = array_merge($digits, $lowercase, $uppercase, $special);

        $password  = str_shuffle(array_rand($digits) . array_rand($lowercase) . array_rand($uppercase) . array_rand($special) . implode(array_rand($combined, rand(6, 6))));

        return str_shuffle($password);
    }

    
}
