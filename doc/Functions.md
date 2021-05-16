# Functions
**Contents**
- [Callable](#Callable)
  - [compose](#compose)
  - [partial and partialLeft](#partial-and-partialLeft)
  - [partialRight](#partialRight)
- [Cast](#Cast)
  - [asArray](#asArray)
  - [asBool](#asBool)
  - [asFloat](#asFloat)
  - [asInt](#asInt)
  - [asList](#asList)
  - [asNonEmptyArray](#asNonEmptyArray)
  - [asNonEmptyList](#asNonEmptyList)
- [Collection](#Collection)
  - [any](#any)
  - [anyOf](#anyOf)
  - [at](#at)
  - [copyCollection](#copyCollection)
  - [every](#every)
  - [everyOf](#everyOf)
  - [filter](#filter)
  - [filterNotNull](#filterNotNull)
  - [filterOf](#filterOf)

# Callable

  - #### compose
    
    Compose functions. Output of one function will be passed as input to
    another function
    
    ``` php
    $aToB = fn(int $a): bool => true;
    $bToC = fn(bool $b): string => (string) $b;
    $cTod = fn(string $c): float => (float) $c;
    
    /** @var callable(int): float $result */
    $result = \Fp\Callable\compose($aToB, $bToC, $cTod);
    ```

  - #### partial and partialLeft
    
    Partial application from first function argument
    
    ``` php
    $callback = fn(int $a, string $b, bool $c): bool => true;
    
    /** @var callable(bool): bool $result */
    $result = \Fp\Callable\partial($callback, 1, "string");
    
    /** @var callable(bool): bool $result */
    $result = \Fp\Callable\partialLeft($callback, 1, "string");
    ```

  - #### partialRight
    
    Partial application from last function argument
    
    ``` php
    $callback = fn(int $a, string $b, bool $c): bool => true;
    
    /** @var callable(int): bool $result */
    $result = \Fp\Callable\partialRight($callback, true, "string");
    ```

# Cast

  - #### asArray
    
    Copy collection as array
    
    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var array<string, int> $result */
    $result = asArray(getCollection());
    ```

  - #### asBool
    
    Try cast boolean like value. Returns None if cast is not possible
    
    ``` php
    /** @var Option<bool> $result */
    $result = asBool('yes');
    ```

  - #### asFloat
    
    Try cast float like value. Returns None if cast is not possible
    
    ``` php
    /** @var Option<float> $result */
    $result = asFloat('1.1');
    ```

  - #### asInt
    
    Try cast integer like value. Returns None if cast is not possible
    
    ``` php
    /** @var Option<int> $result */
    $result = asInt(1);
    ```

  - #### asList
    
    Copy one or multiple collections as list
    
    ``` php
    $result = asList([1], ['prop' => 2], [3, 4]); // [1, 2, 3, 4]
    ```

  - #### asNonEmptyArray
    
    Try copy and cast collection to non-empty-array. Returns None if
    there is no first collection element
    
    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var Option<non-empty-array<string, int>> $result */
    $result = asNonEmptyArray(getCollection());
    ```

  - #### asNonEmptyList
    
    Try copy and cast collection to non-empty-list. Returns None if
    there is no first collection element
    
    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var Option<non-empty-list<int>> $result */
    $result = asNonEmptyList(getCollection());
    ```

# Collection

  - #### any
    
    Returns true if there is collection element which satisfies the
    condition and false otherwise
    
    ``` php
    any(
      [1, 2, 3],
      fn(int $value): bool => $value === 2 
    ); // true
    ```

  - #### anyOf
    
    Returns true if there is collection element of given class and false
    otherwise
    
    ``` php
    anyOf([new Foo(), 2, 3], Foo::class); // true
    ```

  - #### at
    
    Find element by it's key
    
    O(1) for arrays and classes which implement ArrayAccess. O(N) for
    other cases
    
    ``` php
    /** @var Option<Foo|int> $result */
    $result = at([new Foo(), 2, 3], 1);
    ```

  - #### copyCollection
    
    Copy any iterable collection into php array
    
    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var array<string, int> $result */
    $result = copyCollection(getCollection);
    ```

  - #### every
    
    Returns true if every collection element satisfies the condition and
    false otherwise
    
    ``` php
    every([1, 2], fn(int $v): bool => $v === 1); // false
    ```

  - #### everyOf
    
    Returns true if every collection element is of given class false
    otherwise
    
    ``` php
    everyOf([1, new Foo()], Foo::class); // false
    ```

  - #### filter
    
    Filter collection by condition. Preserves keys by default
    
    ``` php
    filter([1, 2], fn(int $v): bool => $v === 2); // [1 => 2]
    ```

  - #### filterNotNull
    
    Filter not null elements. Preserves keys by default
    
    ``` php
    filterNotNull([1, null, 2]); // [0 => 1, 2 => 2]
    ```

  - #### filterOf
    
    Filter elements of given class. Preserves keys by default
    
    ``` php
    /** @var array<0|1|2, Foo> $result */
    $result = filterOf([1, new Foo(), 2], Foo::class);
    ```
