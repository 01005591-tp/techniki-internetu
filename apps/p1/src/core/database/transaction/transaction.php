<?php

namespace p1\core\database\transaction;

use Closure;
use p1\core\function\Either;
use Throwable;

class Transaction {
  private Closure $closure;

  private function __construct($closure) {
    $this->closure = $closure;
  }

  public function andThen(Closure $closure): Transaction {
    return new Transaction(
      fn() => ($closure)(($this->closure)())
    );
  }

  public function execute(TransactionManager $transactionManager): Either {
    try {
      $transactionManager->begin();
      $result = ($this->closure)();
      $transactionManager->commit();
      return Either::ofRight($result);
    } catch (Throwable $throwable) {
      $transactionManager->rollback();
      return Either::ofLeft($throwable);
    }
  }

  public static function of(Closure $closure): Transaction {
    return new Transaction($closure);
  }
}