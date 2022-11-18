# Static analysis

Highly recommended to use this library in tandem with [Psalm](https://github.com/vimeo/psalm).

Psalm is awesome library for static analysis of PHP code.
It opens the road to typed functional programming.

# Psalm plugin

Psalm cannot check everything. But the [plugin system](https://psalm.dev/docs/running_psalm/plugins/authoring_plugins/) allows to improve type inference and implement other custom diagnostics.

To enable plugin shipped with library:

```console
$ composer require --dev fp4php/psalm-toolkit
$ vendor/bin/psalm-plugin enable fp4php/functional
```

# Features

- #### filter
  
  Plugin add type narrowing for filtering.

  `Fp\Functional\Option\Option::filter`:
  
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
  
  `Fp\Collections\ArrayList::filter` (and other collections with `filter` method):
  
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
  
  `Fp\Functional\Either\Either::filterOrElse`:
  
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
  
  `Fp\Collection\filter`:
  
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
  
  `Fp\Collection\first` and `Fp\Collection\last`:
  
  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Collection\first;
  use function Fp\Collection\last;
  
  /**
  * @return list<int|string>
  */
  function getList(): array
  {
      // ...
  }
  
  // Narrowed to Option<string>
  first(getList(), fn($value) => is_string($value));
  
  // Narrowed to Option<int>
  last(getList(), fn($value) => is_int($value));
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
  
- #### fold
  
  Is too difficult to make the fold function using type system of psalm.
  Without plugin `Fp\Collection\fold` and collections fold method has some edge cases. For example: https://psalm.dev/r/b0a99c4912
  
  Plugin can fix that problem.
  
- #### ctor

  PHP 8.1 brings feature called [first-class callable](https://wiki.php.net/rfc/first_class_callable_syntax).
  But that feature cannot be used for class constructor. `Fp\Callable\ctor` can simulate this feature for class constructors, but requires plugin for static analysis.
  
  ```php
  <?php
  
  use Tests\Mock\Foo;
  
  use function Fp\Callable\ctor;
  
  // Psalm knows that ctor(Foo::class) is Closure(int, bool, bool): Foo 
  test(ctor(Foo::class));
  
  /**
   * @param Closure(int, bool, bool): Foo $makeFoo
   */
  function test(Closure $makeFoo): void
  {
      print_r($makeFoo(42, true, false));
      print_r(PHP_EOL);
  }
  ```
  
- #### sequence

  Plugin brings structural type inference for sequence functions:
  
  ```php
  <?php
  
  use Fp\Functional\Option\Option;
  
  use function Fp\Collection\sequenceOption;
  use function Fp\Collection\sequenceOptionT;
  
  function getFoo(int $id): Option
  {
      // ...
  }
  
  function getBar(int $id): Option
  {
      // ...
  }
  
  /**
   * @return Option<array{foo: Foo, bar: Bar}>
   */
  function sequenceOptionShapeExample(int $id): Option
  {
      // Inferred type is: Option<array{Foo, Bar}> not Option<array<'foo'|'bar', Foo|Bar>>
      return sequenceOption([
          'foo' => getFoo($id),
          'bar' => getBar($id),
      ]);
  }
  
  /**
   * @return Option<array{Foo, Bar}>
   */
  function sequenceOptionTupleExample(int $id): Option
  {
      // Inferred type is: Option<array{Foo, Bar}> not Option<list<Foo|Bar>>
      return sequenceOptionT(getFoo($id), getBar($id));
  }
  ```
  
- #### assertion
  
  Unfortunately `@psalm-assert-if-true`/`@psalm-assert-if-false` works incorrectly for Option/Either assertion methods: https://psalm.dev/r/408248f46f
  
  Plugin implements workaround for this bug.
  
- #### N-combinators
  
  Psalm plugin will prevent calling *N combinator in non-valid cases:
  
  ```php
  <?php
  
  declare(strict_types=1);
  
  use Fp\Functional\Option\Option;
  use Tests\Mock\Foo;
  
  /**
   * @param Option<array{int, bool}> $maybeData
   * @return Option<Foo>
   */
  function test(Option $maybeData): Option
  {
      /*
       * ERROR: IfThisIsMismatch
       * Object must be type of Option<array{int, bool, bool}>, actual type Option<array{int, bool}>
       */
      return $maybeData->mapN(fn(int $a, bool $b, bool $c) => new Foo($a, $b, $c));
  }
  ```
  
- #### proveTrue
  
  Implementation assertion effect for `Fp\Evidence\proveTrue` (like for builtin `assert` function):
  
  ```php
  <?php
  
  use Fp\Functional\Option\Option;
  
  function getIntOrString(): int|string
  {
      // ...
  }
  
  Option::do(function() {
      $value = getIntOrString();
      yield proveTrue(is_int($value));
  
      // here $value narrowed to int from int|string
  });
  ```
  
- #### toEither
  
  Inference for `Fp\Functional\Separated\Separated::toEither`:
  
  ```php
  <?php
  
  use Fp\Collections\HashSet;
  use Fp\Collections\ArrayList;
  use Fp\Functional\Either\Either;
  use Fp\Functional\Separated\Separated;
  
  /**
   * @param Separated<ArrayList<int>, ArrayList<string>> $separated
   * @return Either<ArrayList<int>, ArrayList<string>>
   */
  function separatedArrayListToEither(Separated $separated): Either
  {
      return $separated->toEither();
  }
  
  /**
   * @param Separated<HashSet<int>, HashSet<string>> $separated
   * @return Either<HashSet<int>, HashSet<string>>
   */
  function separatedHashSetToEither(Separated $separated): Either
  {
      return $separated->toEither();
  }
  ```
  