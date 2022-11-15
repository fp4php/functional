## Functions BC

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

## Map BC


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

# Map BC
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
`Fp\Collections\Map::toArray` has `@psalm-if-this-is` annotation. You cannot call this method if `Map` key is not `array-key` subtype.

## Removed without alternatives
- Fp\Functional\Validated\Validated
- Fp\Functional\Semigroup\Semigroup
- Fp\Functional\Monoid\Monoid
