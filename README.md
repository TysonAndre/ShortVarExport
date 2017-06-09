ShortVarExport
==============

Introduction
------------

Builds a smaller representation of PHP values as PHP code, similar to `var_export`.

The representation is also valid, executable PHP code.

This is compatible with PHP 7.0+ (Will work in php5 if you remove scalar type hints and return types, and `declare`)
- See https://github.com/TysonAndre/Transphpile to automatically convert code

`ShortVarExport\Builder::build($value)` returns arrays formatted with the php 5.4 square bracket syntax.
This isn't fully tested yet.

Supported data types:

- arrays
- scalars (string/float/int/bool)
- null

Unsupported data types, which will trigger an `InvalidArgumentException` (Support can be added by subclassing the class)
(A subclass may be added in the future, supporting objects)

- resource
- objects

License: MIT

Examples of things this could be used for:
- https://blog.graphiq.com/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad


Usage
-----

```php
require_once '/path/to/Builder.php';
use ShortVarExport\Builder;
echo Builder::build(2, Builder::MULTI_LINE), "\n\n";
echo Builder::build(["value", 42, 4 => [["newvalue"]], "key" => "othervalue"], Builder::MULTI_LINE), "\n";
```

Output

```
2

[
"value",
4 => [["newvalue"]],
"key" => "othervalue",
]
```

Testing
-------

`php BuilderTest.php`
