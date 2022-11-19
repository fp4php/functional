# ArrayList

```Seq<TV>``` interface implementation.

Collection with O(1) ```Seq::at()``` and ```Seq::__invoke()``` operations.

```php
<?php

declare(strict_types=1);

use Fp\Collections\ArrayList;
use Tests\Mock\Foo;

$collection = ArrayList::collect([
    new Foo(1),
    new Foo(2) 
    new Foo(3),
    new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->fold(0)(fn($acc, $elem) => $acc + $elem); // 9
```

