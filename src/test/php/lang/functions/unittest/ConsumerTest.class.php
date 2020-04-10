<?php namespace lang\functions\unittest;

use lang\{ClassCastException, IllegalStateException};
use lang\functions\Consumer;

class ConsumerTest extends \unittest\TestCase {

  #[@test]
  public function void() {
    Consumer::void()->accept(5);
  }

  #[@test]
  public function is_callable() {
    $f= Consumer::void();
    $f(5);
  }

  #[@test, @values([
  #  [function($arg) { return true; }],
  #  ['extension_loaded']
  #])]
  public function of($arg) {
    $this->assertInstanceOf(Consumer::class, Consumer::of($arg));
  }

  #[@test, @expect(ClassCastException::class), @values([
  #  [function() { }],
  #  ['non_existant'],
  #  ['lang.functions.NonExistant::apply']
  #])]
  public function of_does_not_accept_invalid_references($arg) {
    Consumer::of($arg);
  }

  #[@test]
  public function accept() {
    $values= [];
    $add= function($val) use(&$values) { $values[]= $val; };

    Consumer::of($add)->accept(1);
    $this->assertEquals([1], $values);
  }

  #[@test]
  public function butFirst() {
    $operations= [];
    $write= function($val) use(&$operations) { $operations[]= 'Wrote '.$val; };
    $log= function($val) use(&$operations) { $operations[]= 'Logged '.$val; };

    Consumer::of($write)->butFirst($log)->accept('Test');
    $this->assertEquals(['Logged Test', 'Wrote Test'], $operations);
  }

  #[@test]
  public function andThen() {
    $values= [];
    $add= function($val) use(&$values) { $values[]= $val; };
    $remove= function($val) use(&$values) { unset($values[array_search($val, $values)]); };

    Consumer::of($add)->andThen($remove)->accept(1);
    $this->assertEquals([], $values);
  }

  #[@test]
  public function butFirst_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Consumer::void()->butFirst($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }

  #[@test]
  public function andThen_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Consumer::void()->andThen($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }
}