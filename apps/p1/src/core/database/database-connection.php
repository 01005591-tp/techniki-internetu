<?php

namespace p1\core\database;

require_once "core/database/db-connection-properties.php";

use Exception;
use mysqli;

class DatabaseConnection
{
    private DbConnectionProperties $dbConnectionProperties;
    private bool|mysqli $connection;

    public function __construct(DbConnectionProperties $dbConnectionProperties)
    {
        $this->dbConnectionProperties = $dbConnectionProperties;
    }

    /**
     * @throws Exception in case connection could not be created or obtained
     */
    public function connection(): bool|mysqli
    {
        if (!isset($this->connection)) {
            $this->connection = $this->createConnection();
        }
        return $this->connection;
    }

    /**
     * @throws Exception in case connection could not be created
     */
    private function createConnection(): bool|mysqli
    {
        $conn = mysqli_connect(
            $this->dbConnectionProperties->url(),
            $this->dbConnectionProperties->user(),
            $this->dbConnectionProperties->pass(),
            $this->dbConnectionProperties->name()
        );
        if (!$conn) {
            throw new Exception('Could not connect to the database '
                . $this->dbConnectionProperties->url() . ' / '
                . $this->dbConnectionProperties->name());
        }
        return $conn;
    }
}