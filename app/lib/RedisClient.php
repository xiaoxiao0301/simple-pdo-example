<?php

namespace App\lib;

/**
 * Redis操作类
 *
 * Class RedisClient
 * @package lib
 */
class RedisClient
{
    /**
     * 类实例
     * @var null
     */
    private static $instance = null;
    /**
     * 链接对象
     * @var null
     */
    protected static $redisClent = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance->connect();
    }

    /**
     * redis链接对象
     * @return \Redis|null
     */
    public function connect()
    {
        $config = new Config();
        $redisConfig = $config['redis'];
        self::$redisClent = new \Redis();
        self::$redisClent->connect($redisConfig['hostname'], $redisConfig['port']);
        self::$redisClent->auth($redisConfig['auth']);
        self::$redisClent->select($redisConfig['select']);
        return self::$redisClent;
    }

    private function __clone()
    {

    }
}