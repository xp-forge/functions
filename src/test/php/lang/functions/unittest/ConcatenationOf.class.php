<?php namespace lang\functions\unittest;

class ConcatenationOf {
  private $start;

  public function __construct($start) {
    $this->start= $start;
  }

  public function apply($arg) {
    return $this->start.$arg;
  }
}