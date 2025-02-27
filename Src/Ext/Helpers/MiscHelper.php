<?php

function json_get_data($string , $data_type = 'string'){
    if (isset($string) && _strlen($string) > 0 )
    { 
        if ($data_type == 'date')
            return "\"" . date_format(date_create($string),"d/m/Y H:i:s") . "\"";
        else {
            // $string = _str_replace("\n","\\n",$string);
            // $string = _str_replace("\"","\\\"",$string);
            // $string = _str_replace("\\","\\\\",$string);
            $string = \App\Helpers\MiscHelper::eJson($string);
            return "\"" . $string . "\"";
        }
    }
    else
        return "null";
}