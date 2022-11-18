# NonEmptyLinkedList

```NonEmptySeq<TV>``` interface implementation.

Collection with O(1) prepend operation.

```php
<?php

declare(strict_types=1);

use Tests\Mock\Foo;
use Fp\Collections\NonEmptyLinkedList;

$collection = NonEmptyLinkedList::collectNonEmpty([
    new Foo(1),
    new Foo(2) 
    new Foo(3),
    new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->fold(0)(fn($acc, $elem) => $acc + $elem);
```

