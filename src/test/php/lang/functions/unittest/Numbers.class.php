<?php namespace lang\functions\unittest;

class Numbers implements \ArrayAccess {
  private $list;

  /**
   * Creates a new lookup instance
   *
   * @param  int...|double... $list
   */
  public function __construct($list= null) {
    $this->list= func_get_args();
  }

  public function offsetGet($key) {
    return isset($this->list[$key]) ? $this->list[$key] : null;
  }

  public function offsetSet($key, $value) {
    $this->list[$key]= $value;
  }

  public function offsetExists($key) {
    return array_key_exists($key, $this->list);
  }

  public function offsetUnset($key) {
    unset($this->list[$key]);
  }
}