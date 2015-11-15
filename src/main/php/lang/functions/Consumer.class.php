<?php namespace lang\functions;

use lang\FunctionType;

/**
 * Wraps a PHP closure which represents a function that accepts one argument
 * of any type which does not produce a result
 *
 * @test  xp://lang.functions.unittest.ConsumerTest
 */
class Consumer {
  private static $VOID;
  private $closure;

  static function __static() {
    self::$VOID= new self(function($arg) { });
  }

  /** @param php.Closure $backing */
  public function __construct(\Closure $backing) {
    $this->closure= $backing;
  }

  /**
   * Returns a new closure instance
   *
   * @param  var $closure A closure reference
   * @return self
   */
  public static function of($closure) {
    return new self(Functions::$CONSUME->cast($closure));
  }

  /**
   * Returns a void consumer which does nothing
   *
   * @return self
   */
  public static function void() {
    return self::$VOID;
  }

  /**
   * Applies this closure and returns the result
   *
   * @param  var $arg
   * @return void
   */
  public function accept($arg) {
    $this->closure->__invoke($arg);
  }

  /**
   * Invocation overloading
   *
   * @param  var $arg
   * @return void
   */
  public function __invoke($arg) {
    $this->closure->__invoke($arg);
  }

  /**
   * Compose this closure with another closure which gets applied **after**
   * this closure gets applied.
   *
   * @param  var $closure A closure reference
   * @return self
   */
  public function andThen($closure) {
    $func= Functions::$CONSUME->cast($closure);
    if ($this === self::$VOID) {
      return new self($closure);
    } else {
      return new self(function($arg) use($func) {
        $this->closure->__invoke($arg);
        $func->__invoke($arg);
      });
    }
  }

  /**
   * Compose this closure with another closure which gets applied **before**
   * this closure gets applied.
   *
   * @param  var $closure A closure reference
   * @return self
   */
  public function compose($closure) {
    $func= Functions::$CONSUME->cast($closure);
    if ($this === self::$VOID) {
      return new self($closure);
    } else {
      return new self(function($arg) use($func) {
        $func->__invoke($arg);
        $this->closure->__invoke($arg);
      });
    }
  }
}