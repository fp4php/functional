# Functions
**Contents**
- [Callable](#Callable)
  - [compose](#compose)
  - [partial and partialLeft](#partial-and-partialLeft)
  - [partialRight](#partialRight)
- [Collection](#Collection)
  - [exists](#exists)
  - [at](#at)
  - [every](#every)
  - [filter](#filter)
  - [filterNotNull](#filterNotNull)
  - [first](#first)
  - [flatMap](#flatMap)
  - [groupBy](#groupBy)
  - [head](#head)
  - [keys](#keys)
  - [last](#last)
  - [map](#map)
  - [partitionT](#partitionT)
  - [pop](#pop)
  - [fold](#fold)
  - [reindex](#reindex)
  - [reverse](#reverse)
  - [second](#second)
  - [shift](#shift)
  - [tail](#tail)
  - [zip](#zip)
- [Cast](#Cast)
  - [asArray](#asArray)
  - [asBool](#asBool)
  - [asFloat](#asFloat)
  - [asInt](#asInt)
  - [asList](#asList)
  - [asNonEmptyArray](#asNonEmptyArray)
  - [asNonEmptyList](#asNonEmptyList)
- [Evidence](#Evidence)
  - [proveArray](#proveArray)
  - [proveNonEmptyArray](#proveNonEmptyArray)
  - [proveList](#proveList)
  - [proveNonEmptyList](#proveNonEmptyList)
  - [proveBool](#proveBool)
  - [proveTrue](#proveTrue)
  - [proveFalse](#proveFalse)
  - [proveString](#proveString)
  - [proveNonEmptyString](#proveNonEmptyString)
  - [proveCallableString](#proveCallableString)
  - [proveClassString](#proveClassString)
  - [proveClassStringOf](#proveClassStringOf)
  - [proveFloat](#proveFloat)
  - [proveInt](#proveInt)
  - [proveOf](#proveOf)

# Callable

  - #### compose
    
    Compose functions. Output of one function will be passed as input to
    another function.
    
    ``` php
    $aToB = fn(int $a): bool => true;
    $bToC = fn(bool $b): string => (string) $b;
    $cTod = fn(string $c): float => (float) $c;
    
    /** @var callable(int): float $result */
    $result = compose($aToB, $bToC, $cTod);
    ```

  - #### partial and partialLeft
    
    Partial application from first function argument. Pass callback and
    N callback arguments. These N arguments will be locked at
    corresponding places (callback parameters) from left-side and new
    callback will be returned with fewer arguments.
    
    ``` php
    $callback = fn(int $a, string $b, bool $c): bool => true;
    
    /** @var callable(bool): bool $result */
    $result = partial($callback, 1, "string");
    
    /** @var callable(bool): bool $result */
    $result = partialLeft($callback, 1, "string");
    ```

  - #### partialRight
    
    Partial application from last function argument Pass callback and N
    callback arguments. These N arguments will be locked at
    corresponding places (callback parameters) from right-side and new
    callback will be returned with fewer arguments.
    
    ``` php
    $callback = fn(int $a, string $b, bool $c): bool => true;
    
    /** @var callable(int): bool $result */
    $result = partialRight($callback, true, "string");
    ```

# Collection

  - #### exists
    
    Returns true if there is collection element which satisfies the
    condition and false otherwise
    
    ``` php
    <?php
    
    use function Fp\Collection\exists;
    
    exists([1, 2, 3], fn(int $value) => $value === 2); // true
    ```

  - #### at
    
    Find element by its key
    
    O(1) for arrays and classes which implement ArrayAccess. O(N) for
    other cases
    
    ``` php
    <?php
    
    use function Fp\Collection\at;
    
    /** @var Option<Foo|int> $result */
    $result = at([new Foo(), 2, 3], 1);
    ```

  - #### every
    
    Returns true if every collection element satisfies the condition and
    false otherwise
    
    ``` php
    <?php
    
    use function Fp\Collection\every;
    
    every([1, 2], fn(int $v) => $v === 1); // false
    ```

  - #### filter
    
    Filter collection by condition. Do not preserve keys by default
    
    ``` php
    <?php
    
    use function Fp\Collection\filter;
    
    filter([1, 2], fn(int $v): bool => $v === 2); // [2]
    ```

  - #### filterNotNull
    
    Filter not null elements. Do not preserve keys by default
    
    ``` php
    <?php
    
    use function Fp\Collection\filterNotNull;
    
    filterNotNull([1, null, 2]); // [1, 2]
    ```

  - #### first
    
    Find first element which satisfies the condition
    
    ``` php
    <?php
    
    use function Fp\Collection\first;
    
    /** @var Option<int> $result */
    $result = first([1, 2], fn(int $v): bool => $v === 2);
    ```

  - #### flatMap
    
    Flat map Consists of map and flatten operations
    
    ``` php
    <?php
    
    use function Fp\Collection\flatMap;
    
    /**
     * 1) map [1, 4] to [[0, 1, 2], [3, 4, 5]]
     * 2) flatten [[0, 1, 2], [3, 4, 5]] to [0, 1, 2, 3, 4, 5]
     */
    flatMap([1, 4], fn(int $x) => [$x - 1, $x, $x + 1]); // [0, 1, 2, 3, 4, 5]
    ```

  - #### groupBy
    
    Group collection elements by key returned by function
    
    ``` php
    <?php
    
    use function Fp\Collection\groupBy;
    
    $result = groupBy([1, 2, 3], fn(int $v): int => $v); // [1 => [1], 2 => [2], 3 => [3]] 
    ```

  - #### head
    
    Returns collection first element
    
    ``` php
    <?php
    
    use function Fp\Collection\head;
    
    $result = head([1, 2, 3]); // Some(1)
    $result = head([]); // None
    ```

  - #### keys
    
    Returns list of collection keys
    
    ``` php
    <?php
    
    use function Fp\Collection\keys;
    
    keys(['a' => 1, 'b' => 2]); // ['a', 'b']
    ```

  - #### last
    
    Returns last collection element and None if there is no last element
    
    ``` php
    <?php
    
    use function Fp\Collection\last;
    
    /** @var Option<int> $result */
    $result = last([1, 2, 3]);
    ```

  - #### map
    
    Produces a new array of elements by mapping each element in
    collection through a transformation function (callback).
    
    ``` php
    <?php
    
    use function Fp\Collection\map;
    
    map([1, 2, 3], fn(int $v) => (string) $v); // ['1', '2', '3']
    ```

  - #### partitionT
    
    Divide collection by given classes
    
    ``` php
    <?php
    
    use Tests\Mock\Foo;
    use Tests\Mock\Bar;
    use function Fp\Collection\partitionT;
    
    // inferred as array{list<Foo>, list<Bar>, list<Foo|Bar>}
    $result = partitionT(
      [new Foo(), new Bar()],
      fn($i) => $i instanceof Foo,
      fn($i) => $i instanceof Bar, 
    );
    ```

  - #### pop
    
    Pop last collection element and return tuple containing this element
    and other collection elements. If there is no last element then
    returns None
    
    ``` php
    <?php
    
    use function Fp\Collection\pop;
    
    [$head, $tail] = pop([1, 2, 3])->get(); // [3, [1, 2]]
    ```
    
    ``` php
    <?php
    
    use Fp\Functional\Option\Option;
    use function Fp\Collection\pop;
    
    Option::do(function () use ($collection) {
      [$head, $tail] = yield pop($collection);
      return doSomethingWithHeadAndTail($head, $tail);
    })   
    ```

  - #### fold
    
    Fold multiple elements into one. Returns None for empty collection
    
    ``` php
    <?php
    
    use function Fp\Collection\fold;
    
    $result = fold('', ['a', 'b', 'c'])(fn($acc, string $cur) => $acc . $cur); // 'abc'
    ```

  - #### reindex
    
    Produces a new array of elements by assigning the values to keys
    generated by a transformation function (callback).
    
    ``` php
    <?php
    
    use function Fp\Collection\reindex;
    
    reindex([1, 'a' => 2], fn (int $value) => $value); // [1 => 1, 2 => 2]
    ```

  - #### reverse
    
    Copy collection in reversed order
    
    ``` php
    <?php
    
    use function Fp\Collection\reverse;
    
    reverse([1, 2, 3]); // [3, 2, 1]   
    ```

  - #### second
    
    Returns second collection element. None if there is no second
    collection element
    
    ``` php
    <?php
    
    use function Fp\Collection\second;
    
    second([1, 2, 3])->get(); // 2   
    ```

  - #### shift
    
    Shift first collection element and return tuple containing this
    element and other collection elements. If there is no first element
    then returns None
    
    ``` php
    <?php
    
    use function Fp\Collection\shift;
    
    [$head, $tail] = shift([1, 2, 3])->get(); // [1, [2, 3]]   
    ```
    
    ``` php
    <?php
    
    use Fp\Functional\Option\Option;
    use function Fp\Collection\shift;
    
    Option::do(function () use ($collection) {
      [$head, $tail] = yield shift($collection);
      return doSomethingWithHeadAndTail($head, $tail);
    })   
    ```

  - #### tail
    
    Returns every collection element except first
    
    ``` php
    <?php
    
    use function Fp\Collection\tail;
    
    tail([1, 2, 3]); // [2, 3]   
    ```

  - #### zip
    
    Returns an iterable collection formed from this iterable collection
    and another iterable collection by combining corresponding elements
    in pairs.
    
    If one of the two collections is longer than the other, its
    remaining elements are ignored.
    
    ``` php
    <?php
    
    use function Fp\Collection\zip;
    
    zip([1, 2, 3], ['a', 'b']); // [[1, 'a'], [2, 'b']]
    ```

# Cast

  - #### asArray
    
    Copy collection as array
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asArray;
    
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var array<string, int> $result */
    $result = asArray(getCollection());
    ```

  - #### asBool
    
    Try cast boolean like value. Returns None if cast is not possible
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asBool;
    
    /** @var Option<bool> $result */
    $result = asBool('yes');
    ```

  - #### asFloat
    
    Try cast float like value. Returns None if cast is not possible
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asFloat;
    
    /** @var Option<float> $result */
    $result = asFloat('1.1');
    ```

  - #### asInt
    
    Try cast integer like value. Returns None if cast is not possible
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asInt;
    /** @var Option<int> $result */
    $result = asInt(1);
    ```

  - #### asList
    
    Copy one or multiple collections as list
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asList;
    
    $result = asList([1], ['prop' => 2], [3, 4]); // [1, 2, 3, 4]
    ```

  - #### asNonEmptyArray
    
    Try cast collection to new non-empty-array. Returns None if there is
    no first collection element
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asNonEmptyArray;
    
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var Option<non-empty-array<string, int>> $result */
    $result = asNonEmptyArray(getCollection());
    ```

  - #### asNonEmptyList
    
    Try cast collection to new non-empty-list. Returns None if there is
    no first collection element
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Cast\asNonEmptyList;
    
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var Option<non-empty-list<int>> $result */
    $result = asNonEmptyList(getCollection());
    ```

# Evidence

  - #### proveArray
    
    Prove that given value is of array type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveArray;
    
    function getMixed(): mixed { return []; }
    
    // inferred as Option<array<array-key, mixed>>
    $result = proveArray(getMixed());
    ```
    
    Type params from any iterable type will be preserved:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveArray;
    
    /** @return iterable<string, int> */
    function getCollection(): iterable { return []; }
    
    // inferred as Option<array<string, int>>
    $result = proveArray(getCollection());
    ```
    
    Key and value type can be proved separately:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveArray;
    use function Fp\Evidence\proveString;
    use function Fp\Evidence\proveInt;
    
    /** @return iterable<mixed, mixed> */
    function getCollection(): iterable { return []; }
    
    // inferred as Option<array<string, int>>
    $result = proveArray(getCollection(), proveString(...), proveInt(...));
    ```

  - #### proveNonEmptyArray
    
    Prove that given collection is of non-empty-array type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyArray;
    
    /** @return iterable<string, int> */
    function getCollection(): array { return []; }
    
    // Inferred as Option<non-empty-array<string, int>>
    $result = proveNonEmptyArray(getCollection());
    ```
    
    Type params from any iterable type will be preserved:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyArray;
    
    /** @return iterable<string, int> */
    function getCollection(): iterable { return []; }
    
    // inferred as Option<non-empty-array<string, int>>
    $result = proveNonEmptyArray(getCollection());
    ```
    
    Key and value type can be proved separately:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyArray;
    use function Fp\Evidence\proveString;
    use function Fp\Evidence\proveInt;
    
    /** @return iterable<mixed, mixed> */
    function getCollection(): iterable { return []; }
    
    // inferred as Option<non-empty-array<string, int>>
    $result = proveNonEmptyArray(getCollection(), proveString(...), proveInt(...));
    ```

  - #### proveList
    
    Prove that given value is of list type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveList;
    
    function getMixed(): mixed { return []; }
    
    // Inferred as Option<list<mixed>>
    $result = proveList(getMixed());
    ```
    
    Type params from any iterable type will be preserved:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveList;
    
    /** @return iterable<int, string> */
    function getCollection(): iterable { return []; }
    
    // inferred as Option<list<string>>
    $result = proveList(getCollection());
    ```
    
    Value type can be proved separately:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveList;
    use function Fp\Evidence\proveInt;
    
    function getMixed(): mixed { return []; }
    
    // Inferred as Option<list<int>>
    $result = proveList(getMixed(), proveInt(...));
    ```

  - #### proveNonEmptyList
    
    Prove that given value is of non-empty-list type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyList;
    
    function getMixed(): mixed { return []; }
    
    // Inferred as Option<non-empty-list<mixed>>
    $result = proveNonEmptyList(getMixed());
    ```
    
    Type params from any iterable type will be preserved:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyList;
    
    /** @return iterable<int, string> */
    function getCollection(): iterable { return []; }
    
    // Inferred as Option<non-empty-list<string>>
    $result = proveNonEmptyList(getCollection());
    ```
    
    Value type can be proved separately:
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyList;
    use function Fp\Evidence\proveInt;
    
    function getMixed(): mixed { return []; }
    
    // Inferred as Option<non-empty-list<int>>
    $result = proveNonEmptyList(getMixed(), proveInt(...));
    ```

  - #### proveBool
    
    Prove that subject is of boolean type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveBool;
    
    // Inferred as Option<bool>
    $result = proveBool($subject);
    ```

  - #### proveTrue
    
    Prove that subject is of boolean type, and it's value is true
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveTrue;
    
    // Inferred as Option<true>
    $result = proveTrue($subject);
    ```

  - #### proveFalse
    
    Prove that subject is of boolean type, and it's value is false
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveFalse;
    
    // Inferred as Option<false>
    $result = proveFalse($subject);
    ```

  - #### proveString
    
    Prove that subject is of string type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveString;
    
    // Inferred as Option<string>
    $result = proveString($subject);
    ```

  - #### proveNonEmptyString
    
    Prove that subject is of given class
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveNonEmptyString;
    
    $possiblyEmptyString = '';
    
    // Inferred as Option<non-empty-string>
    $result = proveNonEmptyString($possiblyEmptyString);
    ```

  - #### proveCallableString
    
    Prove that subject is of callable-string type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveCallableString;
    
    // Inferred as Option<callable-string>
    $result = proveCallableString($subject);
    ```

  - #### proveClassString
    
    Prove that subject is of class-string type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveClassString;
    
    // Inferred as Option<class-string>
    $result = proveClassString($subject);
    ```

  - #### proveClassStringOf
    
    Prove that subject is subtype of given class-string
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveClassStringOf;
    
    // Inferred as Option<class-string<Collection>>
    $result = proveClassStringOf(ArrayList::class, Collection::class);
    ```

  - #### proveFloat
    
    Prove that subject is of float type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveFloat;
    
    // Inferred as Option<float>
    $result = proveFloat($subject);
    ```

  - #### proveInt
    
    Prove that subject is of int type
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveInt;
    
    // Inferred as Option<int>
    $result = proveInt($subject);
    ```

  - #### proveOf
    
    Prove that subject is of given class
    
    ``` php
    <?php
    
    declare(strict_types=1);
    
    use function Fp\Evidence\proveOf;
    
    // Inferred as Option<Foo>
    $result = proveOf(new Bar(), Foo::class);
    ```
