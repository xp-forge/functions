<?php namespace lang\functions;

use lang\FunctionType;
use lang\Error;

/**
 * Wraps a PHP closure which represents a function that accepts one argument
 * of any type and produces a boolean result.
 *
 * @test  xp://lang.functions.unittest.PredicateTest
 */
class Predicate {
  use LogicalOps;

  private static $TYPE;
  private $predicate;

  static function __static() {
    self::$TYPE= FunctionType::forName('function(var): bool');
  }

  public function __construct(\Closure $backing) {
    $this->predicate= $backing;
  }

  /**
   * Returns a new predicate instance
   *
   * @param  var $predicate A predicate reference
   * @return self
   */
  public static function of($predicate) {
    return new self(self::$TYPE->cast($predicate));
  }

  /**
   * Tests the predicate
   *
   * @param  var $arg
   * @return bool
   */
  public function test($arg) {
    return $this->predicate->__invoke($arg);
  }

  /**
   * Invocation overloading
   *
   * @param  var $arg
   * @return var
   */
  public function __invoke($arg) {
    return $this->predicate->__invoke($arg);
  }
}