<?php

namespace App\Helpers;

class NumberToWords {
        
        
    public static function convert($num, $currency = null){
        $num = floor(floatval($num) * 100) / 100;
        $major_text = "";
        $minor_text = "";

        if(_strtolower($currency) == "iqd"){
            $major_text = "Iraqi Dinar";
            $minor_text = "Iraqi Dirham";
        } else if (_strtolower($currency) == "usd") {
            $major_text = "Dolar";
            $minor_text = "Cent"; 
        } else {
            $major_text = "";
            $minor_text = "";    
        }

        $majors = $minors = $temp = "";
        
        $place = array("", "", " Thousand ", " Million ", " Billion ", " Trillion ");
        
        $decimalPlace = _strpos($num, ".");
        
        if($decimalPlace > 0){
            //With Minor
            $minors .= self::getTens(substr(substr($num, $decimalPlace + 1) . "00", 0,2));
            
            $num = strval(substr($num, 0, $decimalPlace ));

        }

        $count = 1;

        while($num != ""){
            
            $temp = self::getHundreds(substr($num, (_strlen($num) > 3 ? _strlen($num) - 3 : 0), 3));
            if($temp <> ""){
                $majors = $temp . $place[$count] . $majors;
            }

            if(_strlen($num) > 3){
                $num = substr($num, 0, _strlen($num) -3);
            } else {
                $num = "";
            }
            $count++;

        }

        if(!empty($major_text)){
            if($majors == ""){
                $majors = "No " . $major_text . "s";
            } else if ($majors == "One"){
                $majors = "One $major_text";
            } else {
                $majors = $majors . " " . $major_text . "s";
            }
        }

        if(!empty($minor_text)){
            if($minors == ""){
                $minors = " only";
            } else if ($minors == "One"){
                $minors = "and One $minor_text";
            } else {
                $minors = " and " . $minors . " " . $minor_text . "s";
            }
        }
        
        return $majors . $minors;
    }


    //converts a number from 100 to 999
    private static function getHundreds($num){
        //echo "hunds rec: $num<br>";
        $result = "";
        
        if($num == 0) return $result;

        //echo "x $num<br>";
        $num = substr("000" . $num, -3);
        
        //convert the hundred place
        if(substr($num, 0,1) != "0"){
            $result .= self::getDigit(substr($num, 0,1)) . " Hundred ";
        }
        //echo "x $num<br>";

        //convert the tens and ones place.
        if(substr($num, 1,1) != "0"){
            $result .= self::getTens(substr($num, 1));
        } else {
            $result .= self::getDigit(substr($num, 2));
        }

        return $result;
    }

    private static function getTens($num){
        
        $result = "";

        if(substr($num,0,1) == 1){ //Between 10-19
            switch($num){
                case 10: $result .= "Ten"; break;
                case 11: $result .= "Eleven"; break;
                case 12: $result .= "Twelve"; break;
                case 13: $result .= "Thirteen"; break;
                case 14: $result .= "Fourteen"; break;
                case 15: $result .= "Fifteen"; break;
                case 16: $result .= "Sixteen"; break;
                case 17: $result .= "Seventeen"; break;
                case 18: $result .= "Eighteen"; break;
                case 19: $result .= "Nineteen"; break; 
            }
        } else { // Between 20 - 99
            
            switch(substr($num,0,1)){
                
                case 2: $result .= "Twenty "; break;
                case 3: $result .= "Thirty "; break;
                case 4: $result .= "Forty "; break;
                case 5: $result .= "Fifty "; break;
                case 6: $result .= "Sixty "; break;
                case 7: $result .= "Seventy "; break;
                case 8: $result .= "Eighty "; break;
                case 9: $result .= "Ninety "; break;
            }

            $result .= self::getDigit(substr($num,1,1));
        }

        return $result;
    }

    private static function getDigit($num){
        switch($num){
            case 1: return "One"; break;
            case 2: return "Two"; break;
            case 3: return "Three"; break;
            case 4: return "Four"; break;
            case 5: return "Five"; break;
            case 6: return "Six"; break;
            case 7: return "Seven"; break;
            case 8: return "Eight"; break;
            case 9: return "Nine"; break;
            default: return ""; break;
        }
    }
    
}