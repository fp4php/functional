# Functional PHP
PHP Functional Programming library. Monads and common use functions.

![psalm level](https://shepherd.dev/github/whsv26/functional/level.svg)
![psalm type coverage](https://shepherd.dev/github/whsv26/functional/coverage.svg)
[![phpunit coverage](https://coveralls.io/repos/github/whsv26/functional/badge.svg)](https://coveralls.io/github/whsv26/functional)

## Documentation
- ### [Collections](doc/Collections.md)
- ### [Streams](doc/Streams.md)
- ### [Monads](doc/Monads.md)
- ### [Functions](doc/Functions.md)
- ### [Combinators](doc/Combinators.md)


## Installation

### Composer 

```console
$ composer require fp4php/functional
```

### Enable psalm plugin (optional)
To improve type inference

```console
$ composer require --dev fp4php/psalm-toolkit
$ vendor/bin/psalm-plugin enable fp4php/functional
```

## Overview
Typesafe and concise.

Powerful combination: Collections + Option monad.
```php
class PgSqlCurrencyArrayType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $currencies = Option::fromNullable($value)
            ->filter(fn(mixed $raw) => is_iterable($raw))
            ->getOrElse([]);

        return ArrayList::collect($currencies)
            ->filterOf(Currency::class)
            ->map(fn(Currency $currency) => $currency->getCurrencyCode())
            ->mkString('{', ',', '}');
    }

    /**
     * @return Seq<Currency>
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Seq
    {
        $csv = Option::fromNullable($value)
            ->flatMap(proveString(...))
            ->map(fn(string $pgSqlArray) => trim($pgSqlArray, '{}'))
            ->getOrElse('');

        return ArrayList::collect(explode(',', $csv))
            ->filterMap($this->parseCurrency(...));
    }

    /**
     * @return Option<Currency>
     */
    public function parseCurrency(string $currencyCode): Option
    {
        return Option::try(fn() => Currency::of($currencyCode));
    }
}
```


## Examples

- Type safety
```php
/**
 * Inferred type is NonEmptyLinkedList<1|2|3>
 */
$collection = NonEmptyLinkedList::collectNonEmpty([1, 2, 3]);

/**
 * Inferred type is NonEmptyLinkedList<int>
 * 
 * Literal types are dropped after map transformation,
 * but NonEmpty collection prefix has been kept
 */
$mappedCollection = $collection->map(fn($elem) => $elem - 1);

/**
 * Inferred type is LinkedList<positive-int>
 * NonEmpty prefix has been dropped
 */
$filteredCollection = $mappedCollection->filter(fn(int $elem) => $elem > 0);
```
```php
$source = [new Foo(1), null, new Bar(2)];

/**
 * Inferred type is ArrayList<Foo|Bar>
 * Null type was removed
 * NonEmpty prefix was removed
 */
$withoutNulls = NonEmptyArrayList::collectNonEmpty($source)
    ->filter(fn(Foo|Bar|null $elem) => null !== $elem);

/**
 * Inferred type is ArrayList<Foo>
 * Bar type was removed
 */
$onlyFoos = $withoutNulls->filter(fn($elem) => $elem instanceof Foo);

```

- Covariance
```php
class User {}
class Admin extends User {}

/**
* @param NonEmptyCollection<User> $collection
*/
function acceptUsers(NonEmptyCollection $collection): void {}

/** 
 * @var NonEmptyLinkedList<Admin> $collection 
 */
$collection = NonEmptyLinkedList::collectNonEmpty([new Admin()]);

/**
 * You can pass collection of admins instead of users
 * Because of covariant template parameter
 */
acceptUsers($collection);
```

- Immutability
```php
$originalCollection = LinkedList::collect([1, 2, 3]);

/**
 * $originalCollection won't be changed
 */
$prependedCollection = $originalCollection->prepended(0);

/**
 * $prependedCollection won't be changed
 */
$mappedCollection = $prependedCollection->map(fn(int $elem) => $elem + 1);
```

- Null safety
```php
/**
 * @var Collection<int> $emptyCollection 
 */
$emptyCollection = getEmptyCollection();

/**
 * @return Option<float>
 */
function div(int $a, int $b): Option {
    return 0 === $b 
        ? Option::none()
        : Option::some($a / $b)
}

/**
 * It's possible there is no first collection element above zero
 * or divisor is zero.
 *
 * In this case the execution will short circuit (stop)
 * and no Null Pointer Exception will be thrown.
 */
$emptyCollection
    ->first(fn(int $elem) => $elem > 0)
    ->map(fn(int $elem) => $elem + 1)
    ->flatMap(fn(int $elem) => div($elem, $elem - 1))
    ->getOrElse(0)
```

- Type assertions with Option monad via [PHP generators](https://www.php.net/manual/en/language.generators.syntax.php) based [do-notation](https://en.wikibooks.org/wiki/Haskell/do_notation) implementation
```php
/**
 * Inferred type is Option<Foo> 
 */ 
$maybeFooMaybeNot = Option::do(function() use ($untrusted) {
    $notNull = yield Option::fromNullable($untrusted);
    yield proveTrue(is_array($notNull)); // Inferred type is array<array-key, mixed> 
    $list = yield proveList($notNull); // Inferred type is list<mixed>
    $nonEmptyList = yield proveNonEmptyList($list); // Inferred type is non-empty-list<mixed>
    $nonEmptyListOfFoo = yield proveNonEmptyListOf($nonEmptyList, Foo::class); // Inferred type is non-empty-list<Foo>
    $firstFoo = $nonEmptyListOfFoo[0]; // Inferred type is Foo

    return $firstFoo; // I'm sure it's Foo object
});

/**
 * Inferred type is Foo
 */
$foo = $maybeFooMaybeNot->getOrCall(fn() => new Foo(0))
```


## Contribution

### Build documentation

1) Install dependencies
  ```console
  $ sudo apt install pandoc
  ```
2) Generate **doc** from **src**
  ```console
  $ make
  ```
