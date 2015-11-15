<?php namespace lang\functions;

use lang\FunctionType;
use lang\Type;
use lang\Primitive;

/**
 * Function types used throughout the library
 */
abstract class Functions extends \lang\Object {
  public static $APPLY, $CONSUME, $PREDICATE;

  static function __static() {
    self::$APPLY= new FunctionType([Type::$VAR], Type::$VAR);
    self::$CONSUME= new FunctionType([Type::$VAR], Type::$VOID);
    self::$PREDICATE= new FunctionType([Type::$VAR], Primitive::$BOOL);
  }
}