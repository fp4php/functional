# NonEmptyHashSet

Standard ```NonEmptySet<TV>``` interface implementation.

Collection of unique elements.

Object comparison by default uses spl_object_hash function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as elements in HashSet.

```php
use Tests\Mock\Foo;
use Fp\Collections\NonEmptyHashSet;

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

