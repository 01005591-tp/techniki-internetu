<?php

namespace p1\core\database\transaction;

require_once "core/database/transaction/transaction-manager.php";

use mysqli;

class MysqliTransactionManager implements TransactionManager {
  private mysqli $mysqli;

  public function __construct(mysqli $mysqli) {
    $this->mysqli = $mysqli;
  }

  function begin(): bool {
    return $this->mysqli->begin_transaction();
  }

  function commit(): bool {
    return $this->mysqli->commit();
  }

  function rollback(): bool {
    return $this->mysqli->rollback();
  }
}