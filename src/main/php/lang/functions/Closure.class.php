<?php namespace lang\functions;

use lang\FunctionType;

/**
 * Wraps a PHP closure which represents a function that accepts one argument
 * of any type and produces a result (again, of any type).
 *
 * @test  xp://lang.functions.unittest.ClosureTest
 */
class Closure {
  private static $IDENTITY;
  private $closure;

  static function __static() {
    self::$IDENTITY= new self(function($arg) { return $arg; });
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
    return new self(Functions::$APPLY->cast($closure));
  }

  /**
   * Returns the identity closure, which returns anything given to it
   *
   * @return self
   */
  public static function identity() {
    return self::$IDENTITY;
  }

  /**
   * Applies this closure and returns the result
   *
   * @param  var $arg
   * @return var
   */
  public function apply($arg) {
    return $this->closure->__invoke($arg);
  }

  /**
   * Invocation overloading
   *
   * @param  var $arg
   * @return var
   */
  public function __invoke($arg) {
    return $this->closure->__invoke($arg);
  }

  /**
   * Compose this closure with another closure which gets applied **before**
   * this closure gets applied.
   *
   * @param  var $closure A closure reference
   * @return self
   */
  public function butFirst($closure) {
    $func= Functions::$APPLY->cast($closure);
    if ($this === self::$IDENTITY) {
      return new self($func);
    } else {
      return new self(function($arg) use($func) {
        return $this->closure->__invoke($func->__invoke($arg));
      });
    }
  }

  /**
   * Compose this closure with another closure which gets applied **after**
   * this closure gets applied.
   *
   * @param  var $closure A closure reference
   * @return self
   */
  public function andThen($closure) {
    $func= Functions::$APPLY->cast($closure);
    if ($this === self::$IDENTITY) {
      return new self($func);
    } else {
      return new self(function($arg) use($func) {
        return $func->__invoke($this->closure->__invoke($arg));
      });
    }
  }

  /**
   * Wraps this closure with another closure (think: AroundInvoke)
   *
   * @param  var $closure
   * @return self
   */
  public function wrapIn($closure) {
    $func= Functions::$WRAP->cast($closure);
    return new self(function($arg) use($func) {
      return $func->__invoke($this->closure, $arg);
    });
  }
}