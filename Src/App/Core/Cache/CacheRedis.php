<?php

/**
 * Base class for triggers
 */

namespace App\Core\Cache;

use App\Core\Application;
use Predis\Client;

class CacheRedis implements ICacheHandler {

    private $redis;
    public function __construct() {
        $this->redis = new Client([
            'scheme' => Application::getInstance()->env->get("redis_scheme", "tcp"),
            'host'   => Application::getInstance()->env->get("redis_host", "127.0.0.1"),
            'port'   => Application::getInstance()->env->get("redis_port", 6379),
            'password' => Application::getInstance()->env->get("redis_pwd")
        ]);

    }
    
    public function get($key, $default = null) {
        $key = strtolower($key);

        $data = $this->redis->get($key);
        if ($data !== null) {
            // Data found in the cache
            return unserialize($data);
        }
        // Data not found in the cache
        return null;
    }

    public function set($key, $value, $ttl = 600) {
        $key = strtolower($key);
        
        // Serialize the data before storing it in the cache
        $value = serialize($value);
        $this->redis->setex($key, $ttl, $value);
    }

    public function delete($key) {
        $this->redis->del($key);
    }
}
