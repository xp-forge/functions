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

  /**
   * Returns a closure for list lookup
   *
   * @param  php.ArrayAccess|var[] $list
   * @return lang.functions.Closure
   * @throws lang.IllegalArgumentException
   */
  public static function forList($list) {
    if (func_num_args() > 1) {
      $default= func_get_arg(1);
      if ($list instanceof \ArrayAccess) {
        return Closure::of(function($offset) use($list, $default) {
          return isset($list[$offset]) ? $list[$offset] : $default;
        });
      } else if (is('var[]', $list)) {
        return Closure::of(function($offset) use($list, $default) {
          return array_key_exists($offset, $list) ? $list[$offset] : $default;
        });
      }
    } else {
      if ($list instanceof \ArrayAccess) {
        return Closure::of(function($offset) use($list) {
          if (isset($list[$offset])) return $list[$offset];
          throw new ElementNotFoundException('No element at offset #'.$offset);
        });
      } else if (is('var[]', $list)) {
        return Closure::of(function($offset) use($list) {
          if (array_key_exists($offset, $list)) return $list[$offset];
          throw new ElementNotFoundException('No element at offset #'.$offset);
        });
      }
    }
    throw new IllegalArgumentException('Expected either an array or an object overloading array access');
  }
}