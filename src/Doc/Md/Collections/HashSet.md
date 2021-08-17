# HashSet

Standard ```Set<TV>``` interface implementation.

Collection of unique elements.

Object comparison by default uses ```spl_object_hash``` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as elements in HashSet.

```php
use Fp\Collections\HashSet;

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

$collection = HashSet::collect([
    new Foo(1), new Foo(2), new Foo(2), 
    new Foo(3), new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $elem) => $acc + $elem)
    ->getOrElse(0); // 9

// Check if set contains given element 
$collection(new Foo(2)); // true
```

