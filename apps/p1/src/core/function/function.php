<?php

namespace p1\core\function;

use Closure;
use Exception;

interface Function2 {
  function apply($value);
}

interface Function3 {
  function apply($first, $second);
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

interface Predicate {
  function test($value): bool;
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

class FunctionUtils {
  private function __construct() {
    throw new Exception("Cannot instantiate utility class.");
  }

  public static function runnableToFunction2(Runnable $runnable): Function2 {
    return new RunnableToFunction2Wrapper($runnable);
  }

  public static function function2ToSupplier(Function2 $function2, $param): Supplier {
    return new Function2BasedSupplier($function2, $param);
  }

  public static function consumerToRunnable(Consumer $consumer, $value): Runnable {
    return new ConsumerToRunnable($consumer, $value);
  }

  public static function curryFunction3(Function3 $function, $param): Function2 {
    return new Function3Curried($function, $param);
  }

  public static function runnableOfClosure(Closure $closure): Runnable {
    return new ClosureBasedRunnable($closure);
  }

  public static function predicateOfClosure(Closure $closure): Predicate {
    return new ClosureBasedPredicate($closure);
  }

  public static function consumerOfClosure(Closure $closure): Consumer {
    return new ClosureBasedConsumer($closure);
  }

  public static function supplierOfClosure(Closure $closure): Supplier {
    return new ClosureBasedSupplier($closure);
  }

  public static function function2OfClosure(Closure $closure): Function2 {
    return new ClosureBasedFunction2($closure);
  }

  public static function function3OfClosure(Closure $closure): Function3 {
    return new ClosureBasedFunction3($closure);
  }
}

class RunnableToFunction2Wrapper implements Function2 {
  private Runnable $runnable;

  public function __construct(Runnable $runnable) {
    $this->runnable = $runnable;
  }

  function apply($value) {
    $this->runnable->run();
    return null;
  }
}

class Function2BasedSupplier implements Supplier {
  private Function2 $function2;
  private $param;

  public function __construct(Function2 $function2, $param) {
    $this->function2 = $function2;
    $this->param = $param;
  }

  function supply() {
    return $this->function2->apply($this->param);
  }
}

class ConsumerToRunnable implements Runnable {
  private Consumer $consumer;
  private $value;

  public function __construct(Consumer $consumer, $value) {
    $this->consumer = $consumer;
    $this->value = $value;
  }

  function run(): void {
    $this->consumer->consume($this->value);
  }
}

class Function3Curried implements Function2 {
  private Function3 $function;
  private $first;

  public function __construct(Function3 $function, $first) {
    $this->function = $function;
    $this->first = $first;
  }

  function apply($value) {
    return $this->function->apply($this->first, $value);
  }
}

class ClosureBasedRunnable implements Runnable {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function run(): void {
    ($this->closure)();
  }
}

class ClosureBasedPredicate implements Predicate {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function test($value): bool {
    return ($this->closure)($value);
  }
}

class ClosureBasedConsumer implements Consumer {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function consume($value): void {
    ($this->closure)($value);
  }
}

class ClosureBasedSupplier implements Supplier {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function supply() {
    return ($this->closure)();
  }
}

class ClosureBasedFunction2 implements Function2 {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function apply($value) {
    return ($this->closure)($value);
  }
}

class ClosureBasedFunction3 implements Function3 {
  private Closure $closure;

  public function __construct(Closure $closure) {
    $this->closure = $closure;
  }

  function apply($first, $second) {
    return ($this->closure)($first, $second);
  }
}