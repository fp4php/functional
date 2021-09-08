# ArrayList

```Seq<TV>``` interface implementation.

Collection with O(1) ```Seq::at()``` and ```Seq::__invoke()``` operations.

```php
$collection = ArrayList::collect([
    new Foo(1), new Foo(2) 
    new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $elem) => $acc + $elem)
    ->getOrElse(0); // 9
```

