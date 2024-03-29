# KV combinators

- #### Map

Before v5 `Fp\Collections\Map` used `Fp\Collections\Entry` to represents kv pair.
It was unfriendly for ide (lack autocompletion ability). 

Since v5 `Fp\Collections\Entry` has been removed. Instead, each method of `Fp\Collections\Map` has KV version:

```php
<?php

use Fp\Collections\HashMap;

/**
 * @param HashMap<int, int> $hashMap
 * @return HashMap<int, int>
 */
function addOne(HashMap $hashMap): HashMap
{
    return $hashMap->map(fn(int $value) => $value + 2);
}

/**
 * @param HashMap<int, int> $hashMap
 * @return HashMap<int, int>
 */
function sumWithKeys(HashMap $hashMap): HashMap
{
    return $hashMap->mapKV(fn(int $key, int $value) => $key + $value);
}
```

This makes sense since the key and value are rarely needed at the same time.

- #### Functions

Regular functions has KV combinators too:

```php
<?php

use Fp\Collections\HashMap;

use function Fp\Collection\map;
use function Fp\Collection\mapKV;

/**
 * @param array<int, int> $hashMap
 * @return array<int, int>
 */
function addOne(array $hashMap): array
{
    return map($hashMap, fn(int $value) => $value + 2);
}

/**
 * @param array<int, int> $hashMap
 * @return array<int, int>
 */
function sumWithKeys(array $hashMap): array
{
    return mapKV($hashMap, fn(int $key, int $value) => $key + $value);
}
```

Keys were passed as the second parameter $callback/$predicate prior to v5.
At first glance, it was convenient. This leads to such problems: https://psalm.dev/r/f00c0b19be.
But with v5 there is no problem anymore: https://psalm.dev/r/20e91dfded.
