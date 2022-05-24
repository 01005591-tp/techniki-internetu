<?php

namespace p1\config;

use Exception;

class Config
{
    private static Config $instance;

    const ROOT_DIR = __DIR__;

    function rootDir(): string
    {
        return self::ROOT_DIR;
    }

    private function __construct()
    {
    }

    /**
     * Singleton cloning is forbidden.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Singleton deserialization is forbidden.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot deserialize singleton");
    }

    static function instance(): Config
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
