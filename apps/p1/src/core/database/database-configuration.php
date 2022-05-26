<?php

namespace p1\core\database;

use Exception;

require_once "core/database/db-connection-properties.php";
require_once "core/database/database-connection.php";

class DatabaseConfiguration
{
    private static DatabaseConfiguration $instance;
    private DbConnectionProperties $dbConnectionProperties;
    private DatabaseConnection $databaseConnection;

    private function __construct()
    {
        $this->dbConnectionProperties = new DbConnectionProperties();
        $this->databaseConnection = new DatabaseConnection($this->dbConnectionProperties);
    }

    public function databaseConnection(): DatabaseConnection
    {
        return $this->databaseConnection;
    }


    // SINGLETON SPECIFIC FUNCTIONS

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

    static function instance(): DatabaseConfiguration
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}