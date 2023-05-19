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

Read more about [plugin](doc/Psalm.md).

```console
$ composer require --dev fp4php/functional-psalm-plugin
$ vendor/bin/psalm-plugin enable Fp\PsalmPlugin\FunctionalPlugin
```

## Overview
Typesafe and concise.

Powerful combination: Collections + Option monad.
```php
<?php

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;

use function Fp\Evidence\of;
use function Fp\Evidence\proveString;

class PgSqlCurrencyArrayType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $currencies = Option::fromNullable($value)
            ->filter(is_iterable(...))
            ->getOrElse([]);

        return ArrayList::collect($currencies)
            ->flatMap(of(Currency::class))
            ->map(fn(Currency $currency) => $currency->getCurrencyCode())
            ->mkString('{', ',', '}');
    }

    /**
     * @return ArrayList<Currency>
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ArrayList
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
<?php

use Fp\Collections\NonEmptyLinkedList;

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
<?php

use Tests\Mock\Foo;
use Tests\Mock\Bar;
use Fp\Collections\NonEmptyArrayList;

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
<?php

use Fp\Collections\NonEmptyLinkedList;

class User {}
class Admin extends User {}

/**
* @param NonEmptyLinkedList<User> $collection
*/
function acceptUsers(NonEmptyLinkedList $collection): void {}

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
<?php

use Fp\Collections\LinkedList;

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
<?php

use Fp\Functional\Option\Option;
use Fp\Collections\ArrayList;

/**
 * @var ArrayList<int> $collection 
 */
$collection = getCollection();

/**
 * @return Option<float>
 */
function div(int $a, int $b): Option
{
    return Option::when(0 !== $b, fn() => $a / $b);
}

/**
 * It's possible there is no first collection element above zero
 * or divisor is zero.
 *
 * In this case the execution will short circuit (stop)
 * and no Null Pointer Exception will be thrown.
 */
$collection
    ->first(fn(int $elem) => $elem > 0)
    ->map(fn(int $elem) => $elem + 1)
    ->flatMap(fn(int $elem) => div($elem, $elem - 1))
    ->getOrElse(0)
```

- [Do-notation](https://en.wikibooks.org/wiki/Haskell/do_notation) via [PHP generators](https://www.php.net/manual/en/language.generators.syntax.php):

```php
<?php

use Tests\Mock\Foo;
use Fp\Functional\Option\Option;

use function Fp\Evidence\proveTrue;
use function Fp\Evidence\proveNonEmptyList;

/**
 * Inferred type is Option<Foo> 
 */ 
$maybeFooMaybeNot = Option::do(function() use ($untrusted) {
    // If $untrusted is not null then bind this value to $notNull
    $notNull = yield Option::fromNullable($untrusted);
 
    // If $notNull is non-empty-list<Tests\Mock\Foo> then bind this value to $nonEmptyListOfFoo 
    $nonEmptyList = yield proveNonEmptyList($notNull, of(Foo::class));

    // Continue computation if $nonEmptyList contains only one element
    yield proveTrue(1 === count($nonEmptyList));

    // I'm sure it's Foo object
    return $nonEmptyList[0];
});

// Inferred type is Tests\Mock\Foo
$foo = $maybeFooMaybeNot->getOrCall(fn() => new Foo(0));
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
