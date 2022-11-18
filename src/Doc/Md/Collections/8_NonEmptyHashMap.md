# NonEmptyHashMap

```NonEmptyMap<TK, TV>``` interface implementation.

Key-value storage.
It's possible to store objects as keys.

Object keys comparison by default uses ```spl_object_hash``` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as keys in HashMap.

```php
<?php

declare(strict_types=1);

use Fp\Collections\NonEmptyHashMap;

final class Foo implements HashContract
{
    public function __construct(
        public readonly int $a,
        public readonly bool $b = true,
    ) {}

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}

$collection = NonEmptyHashMap::collectPairsNonEmpty([
    [new Foo(1), 1],
    [new Foo(2), 2],
    [new Foo(3), 3],
    [new Foo(4), 4]
]);

$collection(new Foo(2))->getOrElse(0); // 2

$collection
    ->map(fn(int $value) => $value + 1)
    ->toList(); // [[1, 2], [2, 3], [3, 4], [4, 5]]
```

