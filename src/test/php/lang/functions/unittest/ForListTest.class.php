<?php namespace lang\functions\unittest;

use lang\functions\Closures;
use lang\functions\Closure;
use lang\IllegalArgumentException;
use lang\ElementNotFoundException;

class ForListTest extends \unittest\TestCase {

  /** @return var[][] */
  private function lists() {
    return [
      [[1, 2, 3]],
      [new Numbers(1, 2, 3)]
    ];
  }

  #[@test, @values([
  #  [[]],
  #  [[1, 2, 3]],
  #  [new Numbers()]
  #])]
  public function forList($arg) {
    $this->assertInstanceOf(Closure::class, Closures::forList($arg));
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  [['key' => 'value']],
  #  [0], [0.0],
  #  [true], [false],
  #  [''], ['test']
  #])]
  public function forList_does_not_accept_non_lists($arg) {
    Closures::forList($arg);
  }

  #[@test, @values('lists')]
  public function existing_key_with_value($arg) {
    $this->assertEquals(1, Closures::forList($arg)->apply(0));
  }

  #[@test, @expect(ElementNotFoundException::class), @values('lists')]
  public function throws_exceptions_for_non_existant_keys($arg) {
    Closures::forList($arg)->apply(-1);
  }

  #[@test, @values('lists')]
  public function does_not_throw_exceptions_when_given_default_value($arg) {
    $this->assertEquals(0, Closures::forList($arg, 0)->apply(-1));
  }

  #[@test, @values('lists')]
  public function does_not_throw_exceptions_when_given_null_default($arg) {
    $this->assertEquals(null, Closures::forList($arg, null)->apply(-1));
  }

  #[@test, @values([
  #  [[null]],
  #  [new Numbers(null)]
  #])]
  public function does_not_throw_exceptions_for_null($arg) {
    $this->assertEquals(null, Closures::forList($arg)->apply(0));
  }
}