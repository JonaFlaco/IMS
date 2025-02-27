<?php

namespace App\Core;

trait FromArray {
    public static function fromArray(array $data = []) {
        $obj = new self;
        
        foreach($data as $key => $value) {
            if(_strpos('\\', $key) !== false || is_object($value))
                continue;
                
            $obj->{$key} = $value;
        }

        return $obj;
    }
}