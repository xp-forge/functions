<?php namespace lang\functions;

use lang\IllegalArgumentException;
use lang\ElementNotFoundException ;

abstract class Closures {

  /**
   * Returns a closure for map lookup
   *
   * @param  php.ArrayAccess|[:var] $map
   * @return lang.functions.Closure
   * @throws lang.IllegalArgumentException
   */
  public static function forMap($map) {
    if (func_num_args() > 1) {
      $default= func_get_arg(1);
      if ($map instanceof \ArrayAccess) {
        return Closure::of(function($key) use($map, $default) {
          return isset($map[$key]) ? $map[$key] : $default;
        });
      } else if (is('[:var]', $map)) {
        return Closure::of(function($key) use($map, $default) {
          return array_key_exists($key, $map) ? $map[$key] : $default;
        });
      }
    } else {
      if ($map instanceof \ArrayAccess) {
        return Closure::of(function($key) use($map) {
          if (isset($map[$key])) return $map[$key];
          throw new ElementNotFoundException('No element by key "'.$key.'"');
        });
      } else if (is('[:var]', $map)) {
        return Closure::of(function($key) use($map) {
          if (array_key_exists($key, $map)) return $map[$key];
          throw new ElementNotFoundException('No element by key "'.$key.'"');
        });
      }
    }
    throw new IllegalArgumentException('Expected either an array or an object overloading array access');
  }
}