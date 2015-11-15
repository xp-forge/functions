<?php namespace lang\functions\unittest;

use lang\functions\Closure;
use lang\ClassCastException;

class ClosureTest extends \unittest\TestCase {

  #[@test]
  public function identity() {
    $this->assertEquals(5, Closure::identity()->apply(5));
  }

  #[@test]
  public function is_callable() {
    $f= Closure::identity();
    $this->assertEquals(5, $f(5));
  }

  #[@test, @values([
  #  [function($arg) { return $arg; }],
  #  ['strlen'],
  #  ['lang.functions.Closure::of'],
  #  [[Closure::class, 'of']],
  #  [[ConcatenationOf::class, 'new']],
  #  [[new ConcatenationOf('test'), 'apply']]
  #])]
  public function of($arg) {
    $this->assertInstanceOf(Closure::class, Closure::of($arg));
  }

  #[@test, @expect(ClassCastException::class), @values([
  #  [function() { }],
  #  ['non_existant'],
  #  ['lang.functions.NonExistant::apply'],
  #  ['lang.functions.Closure::non_existant'],
  #  [[Closure::class, 'non_existant']],
  #  [[ConcatenationOf::class, 'non_existant']],
  #  [[new ConcatenationOf('test'), 'non_existant']]
  #])]
  public function of_does_not_accept_invalid_references($arg) {
    Closure::of($arg);
  }

  #[@test]
  public function apply() {
    $increment= function($val) { return $val + 1; };

    $this->assertEquals(6, Closure::of($increment)->apply(5));
  }

  #[@test]
  public function compose() {
    $increment= function($val) { return $val + 1; };
    $doubleIt= function($val) { return $val * 2; };

    $this->assertEquals(11, Closure::of($increment)->compose($doubleIt)->apply(5));
  }

  #[@test]
  public function and_then() {
    $increment= function($val) { return $val + 1; };
    $doubleIt= function($val) { return $val * 2; };

    $this->assertEquals(12, Closure::of($increment)->andThen($doubleIt)->apply(5));
  }

  #[@test]
  public function apply_instance() {
    $inCurly= new EnclosedIn('{', '}');
    $inSquare= new EnclosedIn('[', ']');

    $this->assertEquals('[{Test}]', Closure::of([$inCurly, 'layout'])->andThen([$inSquare, 'layout'])->apply('Test'));
  }

  #[@test]
  public function compose_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Closure::identity()->compose($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }

  #[@test]
  public function and_then_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Closure::identity()->andThen($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }
}