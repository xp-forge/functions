<?php namespace lang\functions;

use lang\FunctionType;
use lang\Primitive;
use lang\Type;

/**
 * Function types used throughout the library
 */
abstract class Functions {
  public static $WRAP, $APPLY, $CONSUME, $PREDICATE;

  static function __static() {
    self::$WRAP= new FunctionType([Type::$VAR, Type::$VAR], Type::$VAR);
    self::$APPLY= new FunctionType([Type::$VAR], Type::$VAR);
    self::$CONSUME= new FunctionType([Type::$VAR], Type::$VOID);
    self::$PREDICATE= new FunctionType([Type::$VAR], Primitive::$BOOL);
  }
}