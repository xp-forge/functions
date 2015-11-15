<?php namespace lang\functions;

use lang\Throwable;
use lang\Error;

abstract class Errors {

  public static function suppress() {
    return self::error(function($e) { return null; });
  }

  public static function handle($with) {
    return self::error(Functions::$APPLY->cast($with));
  }

  public static function rethrow($as) {
    $func= Functions::$APPLY->cast($as);
    return self::error(function($e) use($func) { throw $func($e); });
  }

  private static function error($handle) {
    return function($block, $val) use($handle) {
      try {
        return $block($val);
      } catch (Throwable $e) {
        return $handle($e);
      } catch (\Throwable $e) {
        return $handle(new Error(get_class($e).': '.$e->getMessage()));
      }
    };
  }
}