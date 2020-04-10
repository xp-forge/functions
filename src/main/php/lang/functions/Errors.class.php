<?php namespace lang\functions;

use lang\{Error, Throwable};

/**
 * Factory for error handlers to be used in conjunction with `Closure::wrapIn()`.
 *
 * @test  xp://lang.functions.unittest.ErrorsTest
 */
abstract class Errors {

  /**
   * Suppress errors and return null
   *
   * @return function(php.Closure, var): var
   */
  public static function suppress() {
    return self::handleWith(function($e) { return null; });
  }

  /**
   * Handle errors and return what the given handler returns
   *
   * @param  function(lang.Throwable): var $with The handler
   * @return function(php.Closure, var): var
   */
  public static function handle($with) {
    return self::handleWith(Functions::$APPLY->cast($with));
  }

  /**
   * Handle errors and throw what the given handler returns
   *
   * @param  function(lang.Throwable): lang.Throwable $as The handler
   * @return function(php.Closure, var): var
   */
  public static function rethrow($as) {
    $func= Functions::$APPLY->cast($as);
    return self::handleWith(function($e) use($func) { throw $func($e); });
  }

  /**
   * Helper method for the above public methods
   *
   * @param  function(lang.Throwable): var $handler
   * @return function(php.Closure, var): var
   */
  private static function handleWith($handler) {
    return function($block, $val) use($handler) {
      try {
        return $block($val);
      } catch (Throwable $e) {
        return $handler($e);
      } catch (\Throwable $e) {
        return $handler(new Error(get_class($e).': '.$e->getMessage()));
      }
    };
  }
}