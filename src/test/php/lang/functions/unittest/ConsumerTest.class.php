<?php namespace lang\functions\unittest;

use lang\functions\Consumer;
use lang\ClassCastException;
use lang\IllegalStateException;

class ConsumerTest extends \unittest\TestCase {

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
  public function compose() {
    $operations= [];
    $write= function($val) use(&$operations) { $operations[]= 'Wrote '.$val; };
    $log= function($val) use(&$operations) { $operations[]= 'Logged '.$val; };

    Consumer::of($write)->compose($log)->accept('Test');
    $this->assertEquals(['Logged Test', 'Wrote Test'], $operations);
  }

  #[@test]
  public function and_then() {
    $values= [];
    $add= function($val) use(&$values) { $values[]= $val; };
    $remove= function($val) use(&$values) { unset($values[array_search($val, $values)]); };

    Consumer::of($add)->andThen($remove)->accept(1);
    $this->assertEquals([], $values);
  }
}