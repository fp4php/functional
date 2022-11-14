# Static analysis

Highly recommended use this library in tandem with [Psalm](https://github.com/vimeo/psalm).

Psalm is awesome library for static analysis of PHP code.
It opens the road to typed functional programming.

# Psalm plugin

Psalm cannot check everything. But the [plugin system](https://psalm.dev/docs/running_psalm/plugins/authoring_plugins/) allows to improve type inference and implement other custom diagnostics.

To enable plugin shipped with library:

```console
$ composer require --dev fp4php/psalm-toolkit
$ vendor/bin/psalm-plugin enable fp4php/functional
```

# Plugin features

### Type narrowing with filtering

- `Fp\Functional\Option\Option::filter`:

```php
<?php

declare(strict_types=1);

use Fp\Functional\Option\Option;

/**
* @return Option<int|string>
 */
function getOption(): Option
{
    // ...
}

// Narrowed to Option<string>

/** @psalm-trace $result */
$result = getOption()->filter(fn($value) => is_string($value));
```

- `Fp\Collections\ArrayList::filter` (and other collections with `filter` method):

```php
<?php

declare(strict_types=1);

use Fp\Collections\ArrayList;

/**
* @return ArrayList<int|string>
 */
function getArrayList(): ArrayList
{
    // ...
}

// Narrowed to ArrayList<string>

/** @psalm-trace $result */
$result = getArrayList()->filter(fn($value) => is_string($value));
```

- `Fp\Functional\Either\Either::filterOrElse`:

```php
<?php

declare(strict_types=1);

use TypeError;
use ValueError;
use Fp\Functional\Either\Either;

/**
* @return Either<ValueError, int|string>
 */
function getEither(): Either
{
    // ...
}

// Narrowed to Either<TypeError|ValueError, string>
getEither()->filterOrElse(
    fn($value) => is_string($value),
    fn() => new TypeError('Is not string'),
);
```

- `Fp\Collection\filter`:

```php
<?php

declare(strict_types=1);

use function Fp\Collection\filter;

/**
* @return list<int|string>
 */
function getList(): array
{
    // ...
}

// Narrowed to list<string>
filter(getList(), fn($value) => is_string($value));
```

For all cases above you can use [first-class callable](https://wiki.php.net/rfc/first_class_callable_syntax) syntax:

```php
<?php

declare(strict_types=1);

use function Fp\Collection\filter;

/**
* @return list<int|string>
 */
function getList(): array
{
    // ...
}

// Narrowed to list<string>
filter(getList(), is_string(...));
```

### Folding

### Ctor

### Sequence

### Option/Either assertions

### *N combinators

### Flow assertions in the do notation

### Separated to Either