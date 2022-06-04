<?php

namespace p1\core\database;

require_once "core/database/db-connection-properties.php";

use Exception;
use mysqli;

class DatabaseConnection {
  private DbConnectionProperties $dbConnectionProperties;
  private mysqli $connection;

  public function __construct(DbConnectionProperties $dbConnectionProperties) {
    $this->dbConnectionProperties = $dbConnectionProperties;
  }

  /**
   * @throws Exception in case connection could not be created or obtained
   */
  public function connection(): mysqli {
    if (!isset($this->connection)) {
      $this->connection = $this->createConnection();
    }
    return $this->connection;
  }

  /**
   * @throws Exception in case connection could not be created
   */
  private function createConnection(): mysqli {
    return new mysqli(
      $this->dbConnectionProperties->url(),
      $this->dbConnectionProperties->user(),
      $this->dbConnectionProperties->pass(),
      $this->dbConnectionProperties->name()
    );
  }
}