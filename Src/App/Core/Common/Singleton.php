<?php

declare(strict_types = 1);

namespace App\Core\Common;

class Singleton {

    private static $instances = [];

    protected function __construct() { }
    
    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

}
