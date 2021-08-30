# NonEmptyHashSet

```NonEmptySet<TV>``` interface implementation.

Collection of unique elements.

Object comparison by default uses spl_object_hash function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as elements in HashSet.

```php
use Fp\Collections\NonEmptyHashSet;

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

$collection = NonEmptyHashSet::collect([
    new Foo(1), new Foo(2), new Foo(2), 
    new Foo(3), new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->reduce(fn($acc, $elem) => $acc + $elem); // 10
    
// Check if set contains given element 
$collection(new Foo(2)); // true
```

