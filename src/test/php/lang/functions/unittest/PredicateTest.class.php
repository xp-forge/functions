<?php namespace lang\functions\unittest;

use lang\functions\Predicate;
use lang\ClassCastException;
use lang\IllegalStateException;

class PredicateTest extends \unittest\TestCase {

  #[@test, @values([
  #  [function($arg) { return true; }],
  #  ['extension_loaded']
  #])]
  public function of($arg) {
    $this->assertInstanceOf(Predicate::class, Predicate::of($arg));
  }

  #[@test, @expect(ClassCastException::class), @values([
  #  [function() { }],
  #  ['non_existant'],
  #  ['lang.functions.NonExistant::apply']
  #])]
  public function of_does_not_accept_invalid_references($arg) {
    Predicate::of($arg);
  }

  #[@test]
  public function test() {
    $greaterThanZero= function($val) { return $val > 0; };

    $this->assertTrue(Predicate::of($greaterThanZero)->test(1));
    $this->assertFalse(Predicate::of($greaterThanZero)->test(0));
  }

  #[@test]
  public function negate() {
    $greaterThanZero= function($val) { return $val > 0; };

    $this->assertFalse(Predicate::of($greaterThanZero)->negate()->test(1));
  }

  #[@test]
  public function logical_and() {
    $greaterThanZero= function($val) { return $val > 0; };
    $lessThanFifty= function($val) { return $val < 50; };

    $this->assertTrue(Predicate::of($greaterThanZero)->and($lessThanFifty)->test(1));
    $this->assertFalse(Predicate::of($greaterThanZero)->and($lessThanFifty)->test(50));
  }

  #[@test]
  public function logical_or() {
    $isNull= function($val) { return null === $val; };
    $greaterThanZero= function($val) { return $val > 0; };

    $this->assertTrue(Predicate::of($isNull)->or($greaterThanZero)->test(null));
    $this->assertTrue(Predicate::of($isNull)->or($greaterThanZero)->test(1));
  }

  #[@test]
  public function and_is_lazy() {
    $identity= function($val) { return $val; };
    $shouldNotBeReached= function($val) { throw new IllegalStateException('Should not be reached'); };

    $this->assertFalse(Predicate::of($identity)->and($shouldNotBeReached)->test(false));
  }

  #[@test]
  public function or_is_lazy() {
    $identity= function($val) { return $val; };
    $shouldNotBeReached= function($val) { throw new IllegalStateException('Should not be reached'); };

    $this->assertTrue(Predicate::of($identity)->or($shouldNotBeReached)->test(true));
  }
}