<?php

namespace p1\core\function;

use Exception;

interface Function2 {
  function apply($value);
}

interface Supplier {
  function supply();
}

interface Consumer {
  function consume($value): void;
}

interface Runnable {
  function run(): void;
}

class FunctionIdentity implements Function2 {
  private static FunctionIdentity $instance;

  function apply($value) {
    return $value;
  }

  // SINGLETON SPECIFIC FUNCTIONS

  /**
   * Singleton cloning is forbidden.
   * @return void
   */
  private function __clone() {}

  /**
   * Singleton deserialization is forbidden.
   * @throws Exception
   */
  public function __wakeup() {
    throw new Exception("Cannot deserialize singleton");
  }

  static function instance(): FunctionIdentity {
    if (!isset(self::$instance)) {
      self::$instance = new static();
    }
    return self::$instance;
  }
}