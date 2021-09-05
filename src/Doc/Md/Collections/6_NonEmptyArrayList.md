# NonEmptyArrayList

```NonEmptyIndexedSeq<TV>``` interface implementation.

Collection with O(1) ```NonEmptySeq::at()``` and ```NonEmptyIndexedSeq::__invoke()``` operations.


```php
$collection = NonEmptyArrayList::collect([
    new Foo(1), new Foo(2) 
    new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->reduce(fn($acc, $elem) => $acc + $elem); // 10
```

