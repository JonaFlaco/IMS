<?php

/**
 * This class reads .env file
 */

namespace App\Core;

class Env {

    private string $filePath;
    private string $fileName;

    public function __construct($fileName = null) {

        $this->filePath = dirname(dirname(dirname(dirname(__FILE__))));
        $this->fileName = empty($fileName) ? ".env" : $fileName;

        //check if .env file exist
        if(!file_exists($this->filePath . DS . $this->fileName)){
            //throw new \App\Exceptions\FileNotFoundException($this->fileName . " file is missing");
            return;
        }

        //load dotenv
        $dotenv = \Dotenv\Dotenv::createImmutable($this->filePath, $this->fileName);
        $dotenv->load();

        //Change key to upper case
        $_ENV = array_change_key_case($_ENV, CASE_LOWER);
        
    }

    public function get($key, $default = null){
        $result = getenv($key);
        if(_strlen($result) > 0){
            return $result;
        }
        
        return $_ENV[_strtolower($key)] ?? $default;
    }

    public function set($key, $value)
    {
        $_ENV[_strtolower($key)] = $value;
    }

}