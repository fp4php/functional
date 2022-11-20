# T combinators

That combinators accepts varargs as input and return tuples.

`Fp\Collection\partitionT`:

```php
<?php

use Tests\Mock\Foo;
use Tests\Mock\Bar;
use Tests\Mock\Baz;

use function Fp\Collection\partitionT;

/**
* @param list<Foo|Bar|Baz> $list
* @return array{list<Foo>, list<Bar>, list<Baz>}
 */
function example(array $list): array
{
    return partitionT($list, fn($i) => $i instanceof Foo, fn($i) => $i instanceof Bar);
}
```

`Fp\Collection\sequenceOptionT`:

```php
<?php

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveString;
use function Fp\Collection\sequenceOptionT;
use function Fp\Collection\at;

/**
 * @param array<string, mixed> $data
 * @return Option<array{string, int}>
 */
function sequenceT(array $data): Option
{
    return sequenceOptionT(
        at($data, 'name')->flatMap(proveString(...)),
        at($data, 'age')->flatMap(proveInt(...)),
    );
}
```

And others.