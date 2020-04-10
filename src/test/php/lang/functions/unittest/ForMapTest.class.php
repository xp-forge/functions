<?php namespace lang\functions\unittest;

use lang\{ElementNotFoundException, IllegalArgumentException};
use lang\functions\{Closure, Closures};

class ForMapTest extends \unittest\TestCase {

  /** @return var[][] */
  private function maps() {
    return [
      [['key' => 'value']],
      [new Lookup(['key' => 'value'])]
    ];
  }

  #[@test, @values([
  #  [[]],
  #  [['key' => 'value']],
  #  [new Lookup()]
  #])]
  public function forMap($arg) {
    $this->assertInstanceOf(Closure::class, Closures::forMap($arg));
  }

  #[@test, @expect(IllegalArgumentException::class), @values([
  #  [[0, 1, 2]],
  #  [0], [0.0],
  #  [true], [false],
  #  [''], ['test']
  #])]
  public function forMap_does_not_accept_non_maps($arg) {
    Closures::forMap($arg);
  }

  #[@test, @values('maps')]
  public function existing_key_with_value($arg) {
    $this->assertEquals('value', Closures::forMap($arg)->apply('key'));
  }

  #[@test, @expect(ElementNotFoundException::class), @values('maps')]
  public function throws_exceptions_for_non_existant_keys($arg) {
    Closures::forMap($arg)->apply('non_existant');
  }

  #[@test, @values('maps')]
  public function does_not_throw_exceptions_when_given_default_value($arg) {
    $this->assertEquals('test', Closures::forMap($arg, 'test')->apply('non_existant'));
  }

  #[@test, @values('maps')]
  public function does_not_throw_exceptions_when_given_null_default($arg) {
    $this->assertEquals(null, Closures::forMap($arg, null)->apply('non_existant'));
  }

  #[@test, @values([
  #  [['key' => null]],
  #  [new Lookup(['key' => null])]
  #])]
  public function does_not_throw_exceptions_for_null($arg) {
    $this->assertEquals(null, Closures::forMap($arg)->apply('key'));
  }
}