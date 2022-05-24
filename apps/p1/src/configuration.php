<?php

namespace p1\configuration;

use Exception;
use p1\view\navbar\NavbarController;

class Configuration
{
    private static Configuration $instance;
    private NavbarController $navbarController;

    private function __construct()
    {
        $this->navbarController = new NavbarController();
    }

    public function navbarController(): NavbarController
    {
        return $this->navbarController;
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

    static function instance(): Configuration
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
