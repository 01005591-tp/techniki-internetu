<?php

namespace p1\configuration;
require_once "core/database/database-connection.php";

use Exception;
use p1\core\database\DatabaseConnection;
use p1\view\navbar\NavbarController;

class Configuration
{
    private static Configuration $instance;
    private NavbarController $navbarController;
    private DatabaseConnection $databaseConnection;

    private function __construct()
    {
        $this->navbarController = new NavbarController();
        $this->databaseConnection = new DatabaseConnection();
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
