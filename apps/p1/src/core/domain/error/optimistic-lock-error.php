<?php

namespace p1\core\domain\error;

use p1\core\domain\Failure;

class OptimisticLockError extends Failure {
  public static function of(string $message): OptimisticLockError {
    return new OptimisticLockError($message);
  }
}