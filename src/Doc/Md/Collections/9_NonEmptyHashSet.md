# NonEmptyHashSet

```NonEmptySet<TV>``` interface implementation.

Collection of unique elements.

Object comparison by default uses `spl_object_hash` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as elements in HashSet.

```php
<?php

declare(strict_types=1);

use Fp\Collections\NonEmptyHashSet;

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

$collection = NonEmptyHashSet::collectNonEmpty([
    new Foo(1),
    new Foo(2),
    new Foo(2), 
    new Foo(3),
    new Foo(3),
    new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->fold(0)(fn($acc, $elem) => $acc + $elem); // 10

/**
 * Check if set contains given element 
 */
$collection(new Foo(2)); // true

/**
 * Check if one set is contained in another set 
 */
$collection->subsetOf(
    NonEmptyHashSet::collectNonEmpty([
        new Foo(1),
        new Foo(2),
        new Foo(3), 
        new Foo(4),
        new Foo(5),
        new Foo(6),
    ]),
); // true
```

