# HashMap

Standard ```Map<TK, TV>``` interface implementation.

Key-value storage.
It's possible to store objects as keys.

Object keys comparison by default uses ```spl_object_hash``` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as keys in HashMap.

```php
use Fp\Collections\HashMap;

/**
 * @implements HashContract<Foo>
 */
class Foo implements HashContract
{
    public function __construct(public int $a)
    {
    }

    public function equals(mixed $rhs): bool
    {
        return $this->a === $rhs->a;
    }

    public function hashCode(): string
    {
        return implode(',', [md5((string) $this->a)]);
    }
}

$collection = HashMap::collect([
    [new Foo(1), 1], [new Foo(2), 2],
    [new Foo(3), 3], [new Foo(4), 4]
]);

[$reducedKeys, $reducedValues] = $collection
    ->map(fn($elem) => $elem + 10)
    ->filter(fn($elem) => $elem > 11)
    ->reindex(fn($elem, Foo $key) => $key->a)
    ->reduce(fn($acc, $elem) => [$acc[0] + $elem[0], $acc[1] + $elem[1]])
    ->getOrElse([0, 0]); // [9, 39]


$collection(new Foo(2))->getOrElse(0); // 2 

// It's possible to use new Foo(2) because Foo class implements HashContract
```

