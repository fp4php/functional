# NonEmptyArrayList

```NonEmptySeq<TV>``` interface implementation.

Collection with O(1) ```NonEmptySeq::at()``` and ```NonEmptySeq::__invoke()``` operations.


```php
<?php

declare(strict_types=1);

use Tests\Mock\Foo;
use Fp\Collections\NonEmptyArrayList;

$collection = NonEmptyArrayList::collectNonEmpty([
    new Foo(1),
    new Foo(2) 
    new Foo(3),
    new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->fold(0)(fn($acc, $elem) => $acc + $elem); // 10
```

