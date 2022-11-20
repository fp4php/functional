## Remove psalm-pure and psalm-immutable

Before v5 all data structures and most function was pure.
Since v5 `@psalm-pure` and `@psalm-immutable` has been removed.
It has many false positives, and it's to difficult use in the real applications.

Some examples with useful code (but with false positives):
- https://psalm.dev/r/e58c340477
- https://psalm.dev/r/de37ebb8ed
- https://psalm.dev/r/627b50d716

Discussion of the problem: https://github.com/vimeo/psalm/issues/8116

This code is invalid since v5:
```php
/**
 * @param ArrayList<int> $list
 * @return ArrayList<int>
 * @psalm-pure
 */
function pureFn(ArrayList $list): ArrayList
{
    // ERROR: ImpureMethodCall
    return $list->map(fn($i) => $i + 2);
}
```

Removing `@psalm-pure` from the `pureFn` fix that problem.

## Functions BC

- All collection functions with `$callback`/`$predicate` params does not allow key as second parameter anymore.
  To use key in the map, filter and other collection functions use *KV combinators.
  Each collection function with `$callback`/`$predicate` has *KV version:
```diff
- \Fp\Collection\map(fn($value, $key) => new Row(id: $key, data: $value));
+ \Fp\Collection\mapKV(fn($key, $value) => new Row(id: $key, data: $value));
```

- Parameter `$preserveKeys` of `Fp\Collection\filter` has been removed. Type of input array will be preserved:
```diff
- \Fp\Collection\filter(['a' => 1, 'b' => 2], fn($value) => $value !== 1, preserveKeys: true);  // result is ['b' => 2]
+ \Fp\Collection\filter(['a' => 1, 'b' => 2], fn($value) => $value !== 1); // result is ['b' => 2]
- \Fp\Collection\filter([1, 2], fn($value) => $value !== 1, preserveKeys: true);  // result is [2]
+ \Fp\Collection\filter([1, 2], fn($value) => $value !== 1); // result is [2]
```

- `Fp\Collection\existsOf` has been removed. Use `Fp\Collection\exists` instead:
```diff
- \Fp\Collection\existsOf($collection, Foo::class)
+ \Fp\Collection\exists($collection, fn(mixed $i) => $i instanceof Foo);
```

- `Fp\Collection\everyOf` has been removed. Use `Fp\Collection\every` instead:
```diff
- \Fp\Collection\everyOf($collection, Foo::class)
+ \Fp\Collection\every($collection, fn(mixed $i) => $i instanceof Foo);
```

- `Fp\Collection\firstOf` has been removed. Use `Fp\Collection\firstMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\firstOf($collection, Foo::class)
+ \Fp\Collection\firstMap($collection, of(Foo::class));
```

- `Fp\Collection\lastOf` has been removed. Use `Fp\Collection\lastMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\lastOf($collection, Foo::class)
+ \Fp\Collection\lastMap($collection, of(Foo::class));
```

- `Fp\Collection\filterOf` has been removed. Use `Fp\Collection\filterMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\filterOf($collection, Foo::class)
+ \Fp\Collection\filterMap($collection, of(Foo::class));
```

- `Fp\Collection\everyMap` has been removed. Use `Fp\Collection\traverseOption`:
```diff
- \Fp\Collection\everyMap($collection, fn($i) => Option::when($i % 2, fn() => $i));
+ \Fp\Collection\traverseOption($collection, fn($i) => Option::when($i % 2, fn() => $i));
```

- `Fp\Collection\reduce` has been removed. Use `Fp\Collection\fold`:
```diff
- \Fp\Collection\reduce($collection, fn($acc, $cur) => $acc + $cur)->getOrElse(0);
+ \Fp\Collection\fold($collection, 0)(fn($acc, $cur) => $acc + $cur);
```

- `Fp\Collection\partitionOf` has been removed. Use `Fp\Collection\partitionT`:
```diff
- \Fp\Collection\partitionOf($collection, Foo::class, Bar::class);
+ \Fp\Collection\partitionT($collection, fn($i) => $i instanceof Foo, fn($i) => $i instanceof Bar);
```

- `Fp\Evidence\proveListOf` has been removed. Use `Fp\Evidence\proveList` and `Fp\Evidence\of`:
```diff
- \Fp\Evidence\proveListOf(getMixed(), Foo::class);
+ \Fp\Evidence\proveList(getMixed(), of(Foo::class));
```

- `Fp\Evidence\proveNonEmptyListOf` has been removed. Use `Fp\Evidence\proveNonEmptyList` and `Fp\Evidence\of`:
```diff
- \Fp\Evidence\proveNonEmptyListOf(getMixed(), Foo::class);
+ \Fp\Evidence\proveNonEmptyList(getMixed(), of(Foo::class));
```

- `Fp\Evidence\proveArrayOf` has been removed. Use `Fp\Evidence\proveList` and `Fp\Evidence\of`:
```diff
- \Fp\Evidence\proveArrayOf(getMixed(), Foo::class);
+ \Fp\Evidence\proveArray(getMixed(), vType: of(Foo::class));
```

- `Fp\Evidence\proveNonEmptyArrayOf` has been removed. Use `Fp\Evidence\proveNonEmptyList` and `Fp\Evidence\of`:
```diff
- \Fp\Evidence\proveNonEmptyArrayOf(getMixed(), Foo::class);
+ \Fp\Evidence\proveNonEmptyArray(getMixed(), vType: of(Foo::class));
```

- `Fp\Json\jsonDecode` moved to `Fp\Util\jsonDecode`:
```diff
- \Fp\Json\jsonDecode('[1,2,3]');
+ \Fp\Util\jsonDecode('[1,2,3]');
```

- `Fp\String\regExpMatch` moved to `Fp\Util\regExpMatch`:
```diff
- \Fp\String\regExpMatch('/[a-z]+(?<num>[0-9]+)/', 'aa1123');
+ \Fp\Util\regExpMatch('/[a-z]+(?<num>[0-9]+)/', 'aa1123');
```

- `Fp\Reflection\getReflectionClass` has been removed without replacement.
- `Fp\Reflection\getReflectionProperty` has been removed without replacement.

## Option BC
- `Fp\Functional\Option\Option::filterOf` has been removed. Use `Fp\Functional\Option\Option::flatMap` and `Fp\Evidence\of`:
```diff
- $option->filterOf(Foo::class);
+ $option->flatMap(of(Foo::class));
```

- `Fp\Functional\Option\Option::getOrThrow` has been removed. Use `Fp\Functional\Option\Option::getOrCall`:
```diff
- $option->getOrThrow(fn() => new RuntimeExeption());
+ $option->getOrCall(fn() => throw new RuntimeExeption());
```

- `Fp\Functional\Option\Option::isEmpty` has been removed. Use `Fp\Functional\Option\Option::isNone`:
```diff
- $option->isEmpty();
+ $option->isNone();
```

- `Fp\Functional\Option\Option::isNonEmpty` has been removed. Use `Fp\Functional\Option\Option::isSome`:
```diff
- $option->isNonEmpty();
+ $option->isSome();
```

- `Fp\Functional\Option\Option::cond` has been removed. Use `Fp\Functional\Option\Option::when`:
```diff
- \Fp\Functional\Option\Option::cond(getTrue(), doSomething());
+ \Fp\Functional\Option\Option::when(getTrue(), fn() => doSomething());
```

- `Fp\Functional\Option\Option::unless` has been removed. Use `Fp\Functional\Option\Option::when`:
```diff
- \Fp\Functional\Option\Option::unless(getFalse(), fn() => doSomething());
+ \Fp\Functional\Option\Option::when(!getFalse(), fn() => doSomething());
```

- `Fp\Functional\Option\Option::condLazy` has been removed. Use `Fp\Functional\Option\Option::when`:
```diff
- \Fp\Functional\Option\Option::condLazy(getTrue(), fn() => doSomething());
+ \Fp\Functional\Option\Option::when(getTrue(), fn() => doSomething());
```

- Order of `Fp\Functional\Option\Option::fold` params was changed:
```diff
- $option->fold(fn($some) => doSomethingWhenSome($some) fn() => doSomethingWhenNone());
+ $option->fold(fn() => doSomethingWhenNone(), fn($some) => doSomethingWhenSome($some));
```

## Either BC
- `Fp\Functional\Either\Either::condLazy` has been removed. Use `Fp\Functional\Either\Either::when`:
```diff
- \Fp\Functional\Either\Either::condLazy(getBool(), fn() => trueToRight(), fn() => falseToLeft());
+ \Fp\Functional\Either\Either::when(getBool(), fn() => trueToRight(), fn() => falseToLeft());
```

- `Fp\Functional\Either\Either::cond` has been removed. Use `Fp\Functional\Either\Either::when`:
```diff
- \Fp\Functional\Either\Either::cond(getBool(), trueToRight(), falseToLeft());
+ \Fp\Functional\Either\Either::when(getBool(), fn() => trueToRight(), fn() => falseToLeft());
```

- Order of `Fp\Functional\Either\Either::fold` params has been changed:
```diff
- $either->fold(fn($right) => doSomethingWhenRight($right), fn($left) => doSomethingWhenLeft($left));
+ $either->fold(fn($left) => doSomethingWhenLeft($left), fn($right) => doSomethingWhenRight($right));
```

# Seq BC
- `Fp\Collections\Seq::unique` has been removed. Use `Fp\Collections\Seq::uniqueBy` instead:
```diff
- $seq->unique(fn(Foo $foo) => $foo->a);
+ $seq->uniqueBy(fn(Foo $foo) => $foo->a);
```

# Map BC
- `Fp\Collections\Entry` has been removed. To use key in the map, filter and other `Fp\Collections\Map` operations use *KV combinators:
```diff
- $map->map(fn(Entry $kv) => new Row(id: $kv->key, data: $kv->value));
+ $map->mapKV(fn(string $key, array $row) => new Row(id: $key, data: $row));
```
Each `Fp\Collections\Map` operation has *KV version.

- Iteration with foreach has been changed:
```diff
- foreach($map as [$k, $v]) {}
+ foreach($map as $k => $v) {}
```

- `Fp\Collections\Map::toAssocArray` has been removed. Use `Fp\Collections\Map::toArray` instead:
```diff
- $map->toAssocArray()->getOrElse([]);
+ $map->toArray();
```
`Fp\Collections\Map::toArray` has `@psalm-if-this-is` annotation. It impossibly to call this method if `Map` key is not `array-key` subtype.

- `Fp\Collections\Map::mapKeys` has been removed. Use `Fp\Collections\Map::reindex` instead:
```diff
- $map->mapKeys(fn(Entry $kv) => $kv->value->a);
+ $map->reindex(fn(Foo $foo) => $foo->a);
```

- Alias `Fp\Collections\Map::mapValues` has been removed. Use `Fp\Collections\Map::map` instead:
```diff
- $map->mapValues(fn(Entry $kv) => $kv->value->a);
+ $map->map(fn(Foo $foo) => $foo->a);
```

- Method `Fp\Collections\Map::updated` has been renamed to `Fp\Collections\Map::appended`.
```diff
- $map->updated($key, $value);
+ $map->appended($key, $value);
```

## Set BC
- `Fp\Collections\Set::updated` has been removed. Use `Fp\Collections\Set::appended`:
```diff
- $set->updated(new Foo(a: 42));
+ $set->appended(new Foo(a: 42));
```

## Stream BC

- `Fp\Streams\Stream::toAssocArray` has been removed. Use `Fp\Streams\Stream::toArray` instead:
```diff
- $stream->toAssocArray();
+ $stream->toArray();
```

- `Fp\Streams\Stream::repeatN` has been removed. Use `Fp\Streams\Stream::repeat` with `$times` parameter:
```diff
- $stream->repeatN(2);
+ $stream->repeat(2);
```

- `Fp\Streams\Stream::sorted` has been removed. This method was hide all elements loading to the memory. Alternative:
```diff
+ $stream->sorted(fn($l, $r) => $l <=> $r);
+ $stream->toArrayList()->sorted(fn($l, $r) => $l <=> $r)->toStream();
```

## Collection common BC
- Method `filterOf` has been removed. Use `filterMap` method and `Fp\Evidence\of` function: 
```diff
- $seq->filterOf(Foo::class);
+ $seq->filterMap(of(Foo::class));
```

- Method `firstOf` has been removed. Use `firstMap` method and `Fp\Evidence\of` function:
```diff
- $seq->firstOf(Foo::class);
+ $seq->firstMap(of(Foo::class));
```

- Method `lastOf` has been removed. Use `lastMap` method and `Fp\Evidence\of` function:
```diff
- $seq->lastOf(Foo::class);
+ $seq->lastMap(of(Foo::class));
```

- Method `reduce` has been removed. Use `fold` method:
```diff
- $seq->reduce(fn($acc, $cur) => $acc + $cur)->getOrElse(0);
+ $seq->fold(0)(fn($acc, $cur) => $acc + $cur);
```

- Method `everyMap` has been removed. Use `traverseOption` method:
```diff
- $seq->everyMap(fn($i) => Option::when(is_numeric($i), fn() => (int) $i));
+ $seq->traverseOption(fn($i) => Option::when(is_numeric($i), fn() => (int) $i));
```

- Method `existsOf` has been removed. Use `exists` method with predicate:
```diff
- $seq->existsOf(Foo::class);
+ $seq->exists(fn($i) => $i instanceof Foo);
```

- Method `everyOf` has been removed. Use `every` method with predicate:
```diff
- $seq->everyOf(Foo::class);
+ $seq->every(fn($i) => $i instanceof Foo);
```

- Signature of `toHashMap` method has been changed:
```diff
- $seq->toHashMap($i => [$i->key, $i]);
+ $seq->toHashMap();
```
Method `toHashMap` now have `@psalm-if-this-is` annotation.

- Signature of `toArray` method has been changed:
```diff
- $seq->toArray();
+ $seq->toList();
```
Method `toArray` now have `@psalm-if-this-is` annotation and return `array<TKO, TVO>` instead `list<TV>`.

## Removed without alternatives
- Fp\Functional\Validated\Validated
- Fp\Functional\Semigroup\Semigroup
- Fp\Functional\Monoid\Monoid
