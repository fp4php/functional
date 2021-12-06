# HashSet

```Set<TV>``` interface implementation.

Collection of unique elements.

Object comparison by default uses ```spl_object_hash``` function. If you want to override default comparison behaviour then you need to implement HashContract interface for your classes which objects will be used as elements in HashSet.

```php
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true)
    {
    }

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}

$collection = HashSet::collect([
    new Foo(1), new Foo(2), new Foo(2), 
    new Foo(3), new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $elem) => $acc + $elem)
    ->getOrElse(0); // 9

/**
 * Check if set contains given element
 */ 
$collection(new Foo(2)); // true

/**
 * Check if one set is contained in another set 
 */
$collection->subsetOf(HashSet::collect([
    new Foo(1), new Foo(2), new Foo(3), 
    new Foo(4), new Foo(5), new Foo(6),
])); // true
```

- Easy to move from MANY to ONE for many-to-one relations
```php
class Ceo
{
    public function __construct(public string $name) { }
}

class Manager
{
    public function __construct(public string $name, public Ceo $ceo) { }
}

class Developer
{
    public function __construct(public string $name, public Manager $manager) { }
}

$ceo = new Ceo('CEO');
$managerX = new Manager('Manager X', $ceo);
$managerY = new Manager('Manager Y', $ceo);
$developerA = new Developer('Developer A', $managerX);
$developerB = new Developer('Developer B', $managerX);
$developerC = new Developer('Developer C', $managerY);

HashSet::collect([$developerA, $developerB, $developerC])
    ->map(fn(Developer $developer) => $developer->manager)
    ->map(fn(Manager $manager) => $manager->ceo)
    ->tap(fn(Ceo $ceo) => print_r($ceo->name . PHP_EOL)); // CEO. Not CEOCEOCEO
```