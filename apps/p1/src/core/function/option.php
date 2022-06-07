<?php

namespace p1\core\function;

require_once "exceptions/unsupported-operation-exception.php";
require_once "core/function/function.php";

use Exception;
use p1\exceptions\UnsupportedOperationException;

/**
 * Maybe/Option monad type
 */
abstract class Option {
  public abstract function get();

  public abstract function isDefined(): bool;

  public function isNone(): bool {
    return !$this->isDefined();
  }

  public function map(Function2 $function): Option {
    return $this->isDefined()
      ? Option::of($function->apply($this->get()))
      : $this;
  }

  public function flatMap(Function2 $function): Option {
    return $this->isDefined()
      ? $function->apply($this->get())
      : $this;
  }

  public function peek(Consumer $consumer): Option {
    if ($this->isDefined()) {
      $consumer->consume($this->get());
    }
    return $this;
  }

  public function onEmpty(Runnable $runnable): Option {
    if ($this->isNone()) {
      $runnable->run();
    }
    return $this;
  }

  public function filter(Predicate $predicate): Option {
    if ($this->isNone()) {
      return $this;
    }
    return $predicate->test($this->get())
      ? $this
      : Option::none();
  }

  public function orElse($other) {
    return $this->isDefined()
      ? $this->get()
      : $other;
  }

  public function orElseGet(Supplier $otherSupplier) {
    return $this->isDefined()
      ? $this->get()
      : $otherSupplier->supply();
  }

  public function fold(Supplier $ifNone, Function2 $ifDefined) {
    return $this->isDefined()
      ? $ifDefined->apply($this->get())
      : $ifNone->supply();
  }

  public static function of($value): Option {
    return isset($value)
      ? new OptionSome($value)
      : OptionNone::instance();
  }

  public static function none(): Option {
    return OptionNone::instance();
  }
}

class OptionSome extends Option {
  private $value;

  public function __construct($value) {
    $this->value = $value;
  }

  public function get() {
    return $this->value;
  }

  public function isDefined(): bool {
    return true;
  }

  public function __toString(): string {
    return 'OptionSome(' . strval($this->value) . ')';
  }


}

class OptionNone extends Option {
  private static OptionNone $instance;

  public function get() {
    throw new UnsupportedOperationException("OptionNone does not store any value");
  }

  public function isDefined(): bool {
    return false;
  }

  public function __toString(): string {
    return 'OptionNone()';
  }

  private function __construct() {}


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

  public static function instance(): OptionNone {
    if (!isset(self::$instance)) {
      self::$instance = new static();
    }
    return self::$instance;
  }
}