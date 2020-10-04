<?php namespace lang\functions\unittest;

class Lookup implements \ArrayAccess {
  private $map;

  /**
   * Creates a new lookup instance
   *
   * @param  [:var] $map
   */
  public function __construct($map= []) {
    $this->map= $map;
  }

  public function offsetGet($key) {
    return $this->map[$key] ?? null;
  }

  public function offsetSet($key, $value) {
    $this->map[$key]= $value;
  }

  public function offsetExists($key) {
    return array_key_exists($key, $this->map);
  }

  public function offsetUnset($key) {
    unset($this->map[$key]);
  }
}
