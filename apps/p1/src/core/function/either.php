<?php

namespace p1\core\function;

use p1\exceptions\UnsupportedOperationException;

require_once "exceptions/unsupported-operation-exception.php";
require_once "core/function/function.php";

/**
 * Either monad type
 */
abstract class Either {

  public abstract function right();

  public abstract function left();

  public abstract function isRight(): bool;

  public function isLeft(): bool {
    return !$this->isRight();
  }

  public function mapRight(Function2 $function): Either {
    return $this->isRight()
      ? Either::ofRight($function->apply($this->right()))
      : $this;
  }

  public function mapLeft(Function2 $function): Either {
    return $this->isLeft()
      ? Either::ofLeft($function->apply($this->left()))
      : $this;
  }

  public function flatMapRight(Function2 $function): Either {
    return $this->isRight()
      ? $function->apply($this->right())
      : $this;
  }

  public function flatMapLeft(Function2 $function): Either {
    return $this->isLeft()
      ? $function->apply($this->left())
      : $this;
  }

  public function peekRight(Consumer $consumer): Either {
    if ($this->isRight()) {
      $consumer->consume($this->right());
    }
    return $this;
  }

  public function peekLeft(Consumer $consumer): Either {
    if ($this->isLeft()) {
      $consumer->consume($this->left());
    }
    return $this;
  }

  public function fold(Function2 $ifLeft, Function2 $ifRight) {
    return $this->isRight()
      ? $ifRight->apply($this->right())
      : $ifLeft->apply($this->left());
  }

  public function transform(Function2 $transformer) {
    return $transformer->apply($this);
  }

  public function then(Runnable $runnable): Either {
    $runnable->run();
    return $this;
  }

  public static function ofRight($value): Either {
    return new EitherRight($value);
  }

  public static function ofLeft($value): Either {
    return new EitherLeft($value);
  }

  public static function leftSupplier($value): Supplier {
    return new EitherLeftSupplier($value);
  }

  public static function wrapToRightFunction(): Function2 {
    return new EitherRightFunction();
  }
}

class EitherRight extends Either {
  private $value;

  public function __construct($value) {
    $this->value = $value;
  }

  public function right() {
    return $this->value;
  }

  public function left() {
    throw new UnsupportedOperationException("EitherRight does not store left value");
  }

  public function isRight(): bool {
    return true;
  }
}

class EitherLeft extends Either {
  private $value;

  public function __construct($value) {
    $this->value = $value;
  }

  public function right() {
    throw new UnsupportedOperationException("EitherLeft does not store right value");
  }

  public function left() {
    return $this->value;
  }

  public function isRight(): bool {
    return false;
  }
}

class EitherLeftSupplier implements Supplier {
  private $value;

  public function __construct($value) {
    $this->value = $value;
  }

  function supply(): Either {
    return Either::ofLeft($this->value);
  }
}

class EitherRightFunction implements Function2 {
  function apply($value): Either {
    return Either::ofRight($value);
  }
}