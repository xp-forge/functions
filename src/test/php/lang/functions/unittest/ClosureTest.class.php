<?php namespace lang\functions\unittest;

use lang\ClassCastException;
use lang\functions\Closure;
use unittest\{Expect, Test, Values, TestCase};

class ClosureTest extends TestCase {

  /** @return iterable */
  private function closures() {
    yield [function($arg) { return $arg; }];
    yield ['strlen'];
    yield ['lang.functions.Closure::of'];
    yield [[Closure::class, 'of']];
    yield [[ConcatenationOf::class, 'new']];
    yield [[new ConcatenationOf('test'), 'apply']];
  }

  /** @return iterable */
  private function invalid() {
    yield [function() { }];
    yield ['non_existant'];
    yield ['lang.functions.NonExistant::apply'];
    yield ['lang.functions.Closure::non_existant'];
    yield [[Closure::class, 'non_existant']];
    yield [[ConcatenationOf::class, 'non_existant']];
    yield [[new ConcatenationOf('test'), 'non_existant']];
  }

  #[Test]
  public function identity() {
    $this->assertEquals(5, Closure::identity()->apply(5));
  }

  #[Test]
  public function is_callable() {
    $f= Closure::identity();
    $this->assertEquals(5, $f(5));
  }

  #[Test, Values('closures')]
  public function of($arg) {
    $this->assertInstanceOf(Closure::class, Closure::of($arg));
  }

  #[Test, Expect(ClassCastException::class), Values('invalid')]
  public function of_does_not_accept_invalid_references($arg) {
    Closure::of($arg);
  }

  #[Test]
  public function apply() {
    $increment= function($val) { return $val + 1; };

    $this->assertEquals(6, Closure::of($increment)->apply(5));
  }

  #[Test]
  public function butFirst() {
    $increment= function($val) { return $val + 1; };
    $doubleIt= function($val) { return $val * 2; };

    $this->assertEquals(11, Closure::of($increment)->butFirst($doubleIt)->apply(5));
  }

  #[Test]
  public function andThen() {
    $increment= function($val) { return $val + 1; };
    $doubleIt= function($val) { return $val * 2; };

    $this->assertEquals(12, Closure::of($increment)->andThen($doubleIt)->apply(5));
  }

  #[Test]
  public function apply_instance() {
    $inCurly= new EnclosedIn('{', '}');
    $inSquare= new EnclosedIn('[', ']');

    $this->assertEquals('[{Test}]', Closure::of([$inCurly, 'layout'])->andThen([$inSquare, 'layout'])->apply('Test'));
  }

  #[Test]
  public function butFirst_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Closure::identity()->butFirst($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }

  #[Test]
  public function andThen_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Closure::identity()->andThen($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }
}