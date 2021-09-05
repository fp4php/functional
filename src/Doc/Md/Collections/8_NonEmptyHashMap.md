# NonEmptyHashMap

```NonEmptyMap<TK, TV>``` interface implementation.

Key-value storage.
It's possible to store objects as keys.

Object keys comparison by default uses ```spl_object_hash``` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as keys in HashMap.

```php
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true)
    {
    }

    public function equals(mixed $rhs): bool
    {
        return $rhs instanceof self
            && $this->a === $rhs->a
            && $this->b === $rhs->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}

$collection = NonEmptyHashMap::collectNonEmpty([
    [new Foo(1), 1], [new Foo(2), 2],
    [new Foo(3), 3], [new Foo(4), 4]
]);

$collection(new Foo(2))->getOrElse(0); // 2

$collection
    ->map(fn(Entry $entry) => $entry->value + 1)
    ->reindex(fn(Entry $entry) => $entry->key->a)
    ->toArray(); // [[1, 2], [2, 3], [3, 4], [4, 5]]
```

