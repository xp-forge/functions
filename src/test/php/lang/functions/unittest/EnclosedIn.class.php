<?php namespace lang\functions\unittest;

class EnclosedIn {
  private $start, $end;

  public function __construct($start, $end) {
    $this->start= $start;
    $this->end= $end;
  }

  public function layout($arg) {
    return $this->start.$arg.$this->end;
  }
}