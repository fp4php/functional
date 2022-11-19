# LinkedList

```Seq<TV>``` interface implementation.

Collection with O(1) prepend operation.

```php
<?php

declare(strict_types=1);

use Fp\Collections\LinkedList;

$collection = LinkedList::collect([
    new Foo(1), new Foo(2) 
    new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->fold(0)(fn($acc, $elem) => $acc + $elem); // 9
```

