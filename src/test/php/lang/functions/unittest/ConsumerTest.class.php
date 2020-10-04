<?php namespace lang\functions\unittest;

use lang\functions\Consumer;
use lang\{ClassCastException, IllegalStateException};
use unittest\{Expect, Test, Values, TestCase};

class ConsumerTest extends TestCase {

  /** @return iterable */
  private function invalid() {
    yield [function() { }];
    yield ['non_existant'];
    yield ['lang.functions.NonExistant::apply'];
  }

  #[Test]
  public function void() {
    Consumer::void()->accept(5);
  }

  #[Test]
  public function is_callable() {
    $f= Consumer::void();
    $f(5);
  }

  #[Test, Values(eval: '[[function($arg) { return true; }], ["extension_loaded"]]')]
  public function of($arg) {
    $this->assertInstanceOf(Consumer::class, Consumer::of($arg));
  }

  #[Test, Expect(ClassCastException::class), Values('invalid')]
  public function of_does_not_accept_invalid_references($arg) {
    Consumer::of($arg);
  }

  #[Test]
  public function accept() {
    $values= [];
    $add= function($val) use(&$values) { $values[]= $val; };

    Consumer::of($add)->accept(1);
    $this->assertEquals([1], $values);
  }

  #[Test]
  public function butFirst() {
    $operations= [];
    $write= function($val) use(&$operations) { $operations[]= 'Wrote '.$val; };
    $log= function($val) use(&$operations) { $operations[]= 'Logged '.$val; };

    Consumer::of($write)->butFirst($log)->accept('Test');
    $this->assertEquals(['Logged Test', 'Wrote Test'], $operations);
  }

  #[Test]
  public function andThen() {
    $values= [];
    $add= function($val) use(&$values) { $values[]= $val; };
    $remove= function($val) use(&$values) { unset($values[array_search($val, $values)]); };

    Consumer::of($add)->andThen($remove)->accept(1);
    $this->assertEquals([], $values);
  }

  #[Test]
  public function butFirst_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Consumer::void()->butFirst($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }

  #[Test]
  public function andThen_optimized_for_identity() {
    $fixture= function($val) { return 'test'; };

    $closure= Consumer::void()->andThen($fixture);
    $this->assertEquals($fixture, typeof($closure)->getField('closure')->setAccessible(true)->get($closure));
  }
}