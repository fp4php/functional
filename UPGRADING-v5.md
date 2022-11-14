- `Fp\Collection\existsOf` was removed. Use `Fp\Collection\exists` instead: 

```diff
- \Fp\Collection\existsOf($collection, Foo::class)
+ \Fp\Collection\exists($collection, fn(mixed $i) => $i instanceof Foo);
```

- `Fp\Collection\everyOf` was removed. Use `Fp\Collection\every` instead: 

```diff
- \Fp\Collection\everyOf($collection, Foo::class)
+ \Fp\Collection\every($collection, fn(mixed $i) => $i instanceof Foo);
```

- `Fp\Collection\firstOf` was removed. Use `Fp\Collection\firstMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\firstOf($collection, Foo::class)
+ \Fp\Collection\firstMap($collection, of(Foo::class));
```

- `Fp\Collection\lastOf` was removed. Use `Fp\Collection\lastMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\lastOf($collection, Foo::class)
+ \Fp\Collection\lastMap($collection, of(Foo::class));
```

- `Fp\Collection\filterOf` was removed. Use `Fp\Collection\filterMap` and `Fp\Evidence\of`:
```diff
- \Fp\Collection\filterOf($collection, Foo::class)
+ \Fp\Collection\filterMap($collection, of(Foo::class));
```

- `Fp\Collection\everyMap` was removed. Use `Fp\Collection\traverseOption`:
```diff
- \Fp\Collection\everyMap($collection, fn($i) => Option::when($i % 2, fn() => $i));
+ \Fp\Collection\traverseOption($collection, fn($i) => Option::when($i % 2, fn() => $i));
```