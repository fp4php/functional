# *N combinators
- #### Introduction

There are useful combinators for working with shape and tuples:
- `mapN`
- `flatMapN`
- `reindexN`
- And many others

They similar to original `map`/`flatMap`/`reindex` but deconstructs input tuple and pass each value separately.

Look at example with `mapN`:

```php
<?php

declare(strict_types=1);

use Tests\Mock\Foo;
use Fp\Functional\Option\Option;

use function Fp\Collection\at;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\proveArray;
use function Fp\Evidence\proveBool;
use function Fp\Evidence\proveInt;

$json = <<<JSON
{
    "a": 42,
    "b": true,
    "c": false
}
JSON;

/**
 * @return Option<Foo>
 */
function fooFromJson(string $json): Option
{
    return Option::try(fn(): mixed => json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR))
        ->flatMap(proveArray(...))
        ->flatMap(fn(array $data) => sequenceOptionT(
            fn() => at($data, 'a')->flatMap(proveInt(...)),
            fn() => at($data, 'b')->flatMap(proveBool(...)),
            fn() => at($data, 'c')->flatMap(proveBool(...)),
        ))
        ->mapN(fn(int $a, bool $b, bool $c) => new Foo($a, $b, $c));
}
```

It can be described point by point as follows:

1) Decode input json
2) Prove `array<array-key, mixed>` from `mixed`
3) Prove `array{int, bool, bool}` from `array<array-key, mixed>`
4) Use `mapN` deconstruct tuple and crate `Foo`

- #### Omit values from tuple or shape

Rest tuple values can be omitted.
See how to omit unnecessary left values from tuple:

```php
<?php

declare(strict_types=1);

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;

/**
 * @param Option<array{int, bool, bool, string, float}> $maybeData
 * @return Option<Foo>
 */
function omitLeftValues(Option $maybeData): Option
{
    return $maybeData->mapN(fn(int $a, bool $b) => new Foo($a, $b, c: false));
}
```

You can omit any value if your shape contains string keys:

```php
<?php

declare(strict_types=1);

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;

/**
 * @param Option<array{
 *     d: string,
 *     e: float,
 *     a: int,
 *     b: bool,
 *     c: bool
 * }> $maybeData
 * @return Option<Foo>
 */
function omitAnyValueFromShape(Option $maybeData): Option
{
    // Keys 'd', 'e' and 'c' will be ignored
    return $maybeData->mapN(fn(int $a, bool $b) => new Foo($a, $b, c: false));
}
```

In the example above tuple contains only two values. But `mapN` waits three parameters.
This is non-valid case and Psalm tells about it.

- #### Ctor function

There is useful function `Fp\Callable\ctor` that is friend of *N combinators. Examples above can be rewritten as follows:

```php
<?php

declare(strict_types=1);

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;

use function Fp\Callable\ctor;

/**
 * @param Option<array{int, bool, bool}> $maybeData
 * @return Option<Foo>
 */
function ctor(Option $maybeData): Option
{
    return $maybeData->mapN(ctor(Foo::class));
}
```

That function implemented because we can't get function from `__construct` method like for other functions (callable string or first class callable).
That function supports all features meant above (skip unnecessary args, psalm issues)

- #### Caveats

For shapes with string keys the `Fp\Callable\ctor` and *N combinators use `ReflectionFunction` but for tuples not.

For tuples reflection is unnecessary because PHP allows to pass extra arguments to functions with array spread:

```php
<?php

function test(int $a, int $b, int $c): void
{
    print_r('Works' . PHP_EOL);
}

test(...[1, 2, 3, 4]);
// No runtime errors: https://3v4l.org/VUU0i
```

PHP also allows to spread array with string keys for function calling:

```php
<?php

function test(int $a, int $b, int $c): void
{
    print_r('Works' . PHP_EOL);
}
test(...['a' => 1, 'b' => 2, 'c' => 3]);
// No runtime errors (from PHP 8.0): https://3v4l.org/s45Pk
```

But for extra keys PHP throws error (Unknown named parameter):
```php
<?php

function test(int $a, int $b, int $c): void
{
    print_r('Works' . PHP_EOL);
}
test(...['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);
// Runtime error: https://3v4l.org/KFUGP
```

So `ReflectionFunction` used for filtering extra arguments before array will be spread.
