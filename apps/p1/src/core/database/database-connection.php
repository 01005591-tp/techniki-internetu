<?php

namespace p1\core\database;

use Exception;
use mysqli;

class DatabaseConnection
{
    private DbConnectionProperties $dbConnectionProperties;
    private bool|mysqli $connection;

    public function __construct()
    {
        $this->dbConnectionProperties = new DbConnectionProperties();
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

class DbConnectionProperties
{
    private string $url;
    private string $name;
    private string $user;
    private string $pass;

    public function __construct()
    {
        $this->url = getenv("DATABASE_URL");
        $this->name = getenv("DATABASE_NAME");
        $this->user = getenv("DATABASE_USER");
        $this->pass = getenv("DATABASE_PASS");
    }

    public function url(): string
    {
        return $this->url;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function pass(): string
    {
        return $this->pass;
    }
}