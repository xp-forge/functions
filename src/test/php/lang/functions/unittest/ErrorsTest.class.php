<?php namespace lang\functions\unittest;

use lang\functions\{Closure, Errors};
use lang\{IllegalStateException, MethodNotImplementedException};
use unittest\{Expect, Test};

class ErrorsTest extends \unittest\TestCase {

  #[Test]
  public function suppress_exceptions() {
    $fixture= function($val) { throw new IllegalStateException('Test'); };

    $closure= Closure::of($fixture)->wrapIn(Errors::suppress());
    $this->assertNull($closure(null));
  }

  #[Test]
  public function handke_exceptions() {
    $fixture= function($val) { throw new IllegalStateException('Test'); };

    $closure= Closure::of($fixture)->wrapIn(Errors::handle(function($e) { return false; }));
    $this->assertFalse($closure(null));
  }

  #[Test, Expect(IllegalStateException::class)]
  public function rethrow_exceptions() {
    $fixture= function($val) { throw new MethodNotImplementedException('Test', __FUNCTION__); };
    $as= function($e) { return new IllegalStateException('Should not occur', $e); };
    $closure= Closure::of($fixture)->wrapIn(Errors::rethrow($as));
    $closure(null);
  }
}