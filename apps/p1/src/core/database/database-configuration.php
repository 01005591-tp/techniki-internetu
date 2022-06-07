<?php

namespace p1\core\database;

use p1\core\database\transaction\MysqliTransactionManager;
use p1\core\database\transaction\TransactionManager;

require_once "core/database/db-connection-properties.php";
require_once "core/database/database-connection.php";
require_once "core/database/transaction/mysqli-transaction-manager.php";
require_once "core/database/transaction/transaction-manager.php";

class DatabaseConfiguration {
  private DbConnectionProperties $dbConnectionProperties;
  private DatabaseConnection $databaseConnection;
  private TransactionManager $transactionManager;

  public function __construct() {
    $this->dbConnectionProperties = new DbConnectionProperties();
    $this->databaseConnection = new DatabaseConnection($this->dbConnectionProperties);
    $this->transactionManager = new MysqliTransactionManager($this->databaseConnection->connection());
  }

  public function databaseConnection(): DatabaseConnection {
    return $this->databaseConnection;
  }

  public function transactionManager(): TransactionManager {
    return $this->transactionManager;
  }
}