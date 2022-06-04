<?php

namespace p1\exceptions;

use RuntimeException;
use Throwable;

class UnsupportedOperationException extends RuntimeException {
  // override
  public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}