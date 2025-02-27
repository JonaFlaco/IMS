<?php

namespace App\Core;

class MarkDown {

    public static function parse($text) {
        $Parsedown = new \Parsedown();
        
        $value = $Parsedown->text($text);

        $value = _str_replace('<table>','<table class="table"', $value);
        
        return $value;
    }

}