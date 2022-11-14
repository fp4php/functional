```diff
- existsOf($collection, Foo::class)
+ exists($collection, fn(mixed $i) => $i instanceof Foo);
```