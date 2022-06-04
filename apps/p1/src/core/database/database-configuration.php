<?php

namespace p1\core\database;

require_once "core/database/db-connection-properties.php";
require_once "core/database/database-connection.php";

class DatabaseConfiguration {
  private DbConnectionProperties $dbConnectionProperties;
  private DatabaseConnection $databaseConnection;

  public function __construct() {
    $this->dbConnectionProperties = new DbConnectionProperties();
    $this->databaseConnection = new DatabaseConnection($this->dbConnectionProperties);
  }

  public function databaseConnection(): DatabaseConnection {
    return $this->databaseConnection;
  }
}