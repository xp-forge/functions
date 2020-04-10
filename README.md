Functional interfaces
=====================

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/functions.svg)](http://travis-ci.org/xp-forge/functions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/functions/version.png)](https://packagist.org/packages/xp-forge/functions)

Utilities for functional programming:

* **[Closures](#closure)** - Represents a function which takes in an argument and returns a result.
* **[Predicates](#predicate)** - Represents a function which takes in an argument and returns a boolean.
* **[Consumers](#consumer)** - Represents a function which takes in an argument and does not return anything.
* **[Errors](#errors)** - Provides a factory for handling, rethrowing and suppressing errors
* **[Map lookup](#map-lookup)** - Provides a factory for creating closures for map lookups:

Examples
--------
### Closure
Instances of the `lang.functions.Closure` class represent a function which takes in an argument and returns a result.

```php
use lang\functions\Closure;

$increment= Closure::of(function($val) { return $val + 1; });
$doubleIt= Closure::of(function($val) { return $val * 2; });

$increment->apply(5);                      // = 5 + 1 = 6
$increment->andThen($doubleIt)->apply(5);  // = (5 + 1) * 2 = 12
$increment->butFirst($doubleIt)->apply(5); // = (5 * 2) + 1 = 11
```

The `identity()` method returns a closure instance which will return its arguments' value. The following example shows how it is used as the initial value for a reduction on a sequence of filters: If filters is empty, the reduction will return the identity function, otherwise it will combine all given closures using `andThen()`.

```php
use lang\functions\Closure;
use util\data\Sequence;

// Implementation @ https://gist.github.com/thekid/92e020b9f5bbc7e9cb5f
class Color {
  public function __construct(int $r, int $g, int $b) { ... }
  public function brighter(): Color { ... }
  public function darker(): Color { ... }
}

class Camera {
  private $filter;

  public function __construct(... $filters) {
    $this->filter= Sequence::of($filters)->reduce(
      Closure::identity(),
      [Closure::class, 'andThen']
    );
  }

  public function snap(Color $input): Color {
    return $this->filter->apply($input);
  }
}

$input= new Color(125, 125, 125);
(new Camera())->snap($input);                            // 125, 125, 125
(new Camera([Color::class, 'brighter']))->snap($input);  // 178, 178, 178
```

### Predicate
Instances of the `lang.functions.Predicate` class represent a function which takes in an argument and returns a boolean.

```php
use lang\functions\Predicate;

$isNull= Predicate::of(function($val) { return null === $val; });
$gtZero= Predicate::of(function($val) { return $val > 0; });
$ltFifty= Predicate::of(function($val) { return $val < 50; });

$gtZero->test(5);                          // = 5 > 0 = true
$isNull->negate()->test(1);                // = !(1 === null) = true
$gtZero->and($ltFifty)->test(5);           // = 5 > 0 && 5 < 50 = true
$isNull->or($gtZero)->test(null);          // = null === null || null > 0 = true
```

### Consumer
Instances of the `lang.functions.Consumer` class represent a function which takes in an argument and does not return anything.

```php
use lang\functions\Consumer;

$file= ...;
$dump= Consumer::of(function($val) { var_dump($val); });
$write= Consumer::of(function($val) use($file) { $file->write($val); });

$dump->accept(true);                       // Prints "bool(true)"
$dump->andThen($write)->accept(true);      // Prints, then writes to file

if ($simulation) {
  $consumer= Consumer::void();             // Do nothing in simulation
} else if ($debug) {
  $consumer= $write->butFirst($dump);      // In debug mode, log first
} else {
  $consumer= $write;                       // Otherwise, write directly
}
$consumer->accept(true);
```

Consumers can be conveniently used to create APIs like the one below: You cannot forget to close the resource as you might:

```php
use lang\functions\Consumer;

class Resource {
  private $conn;

  private function __construct() { $this->conn= ...; }

  private function close() { $this->conn->close(); }

  public function operation() { ... }

  public static function use(Consumer $consumer) {
    $self= new self();
    try {
      $consumer->accept($self);
    } finally {
      $self->close();
    }
  }
}

Resource::use(Consumer::of(function(Resource $resource) {
  $this->cat->info('Performing operation...')
  $resource->operation();
}));
```

### Errors
For handling errors, closure calls can be wrapped in methods. The `lang.functions.Errors` factory provides three built-in error handling ways:

* **Handle via function**: Supply a closure which receives the exception and can decide to either return a value (e.g. a default), to log and/or rethrow.
* **Rethrow**: Supply a closure to wrap exceptions in other exceptions.
* **Suppress**: Catch exceptions and return *null*.

```php
use lang\functions\Closure;
use lang\functions\Errors;
use lang\Throwable;

$log= function(Throwable $e) {
  $this->cat->error('Operation failed', $e);
  throw $e;
};

Closure::of($operation)->wrapIn(Errors::handle($log))->apply($param);
```

### Map lookup
The `lang.functions.Closures` class provides a factory for creating closures for map lookups:

```php
use lang\functions\Closures;

$map= ['key' => 'value'];

$get= Closures::forMap($map);
$get->apply('key');         // value
$get->apply('color');       // ***lang.ElementNotFoundException

$find= Closures::forMap($map, null);
$find->apply('key');        // value
$find->apply('color');      // null
```

Further reading
---------------
* The [java.util.function package](http://docs.oracle.com/javase/8/docs/api/java/util/function/package-summary.html)
* [Design Patterns in the Light of Lambda Expressions](https://www.youtube.com/watch?v=e4MT_OguDKg) - by Subramaniam at Devoxx 2015
* [Durian: Guava's spikier (unofficial) cousin](https://github.com/diffplug/durian) - Further inspiration