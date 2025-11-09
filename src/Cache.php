<?php

namespace liansu;

use liansu\interfaces\ICacheHandler;

class Cache
{
    protected static ICacheHandler $cacheHandler;
    protected static $expireSeconds = 86400;

    public static function setCacheHandler(ICacheHandler $handler)
    {
        self::$cacheHandler = $handler;
    }

    public static function getCacheHandler(): ICacheHandler
    {
        if (!isset(self::$cacheHandler)) {
            throw new \Exception('No Cache Handler Found');
        }

        return self::$cacheHandler;
    }

    public static function set($key, $value, $handler = null)
    {
        if (!$handler) {
            $handler = self::getCacheHandler();
        }
        $handler->set($key, $value);
    }

    public static function get($key, $default = null, $expireSeconds = null, $handler = null)
    {
        if (!$handler) {
            $handler = self::getCacheHandler();
        }
        $value = $handler->get($key, $default, $expireSeconds);

        return $value;
    }
}
