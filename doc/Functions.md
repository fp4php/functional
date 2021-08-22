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
  - [butLast](#butLast)
  - [every](#every)
  - [everyOf](#everyOf)
  - [exists](#exists)
  - [filter](#filter)
  - [filterNotNull](#filterNotNull)
  - [filterOf](#filterOf)
  - [first](#first)
  - [firstOf](#firstOf)
  - [flatMap](#flatMap)
  - [fold](#fold)
  - [group](#group)
  - [head](#head)
  - [keys](#keys)
  - [last](#last)
  - [map](#map)
  - [partition](#partition)
  - [partitionOf](#partitionOf)
  - [pop](#pop)
  - [reduce](#reduce)
  - [reindex](#reindex)
  - [reverse](#reverse)
  - [second](#second)
  - [shift](#shift)
  - [tail](#tail)
  - [zip](#zip)
- [Evidence](#Evidence)
  - [proveArray](#proveArray)
  - [proveNonEmptyArray](#proveNonEmptyArray)
  - [proveArrayOf](#proveArrayOf)
  - [proveNonEmptyArrayOf](#proveNonEmptyArrayOf)
  - [proveList](#proveList)
  - [proveNonEmptyList](#proveNonEmptyList)
  - [proveListOf](#proveListOf)
  - [proveNonEmptyListOf](#proveNonEmptyListOf)
  - [proveBool](#proveBool)
  - [proveTrue](#proveTrue)
  - [proveFalse](#proveFalse)
  - [proveString](#proveString)
  - [proveNonEmptyString](#proveNonEmptyString)
  - [proveCallableString](#proveCallableString)
  - [proveClassString](#proveClassString)
  - [proveFloat](#proveFloat)
  - [proveInt](#proveInt)
  - [proveOf](#proveOf)
- [Json](#Json)
  - [jsonDecode](#jsonDecode)
  - [jsonSearch](#jsonSearch)
- [Reflection](#Reflection)
  - [getNamedTypes](#getNamedTypes)
  - [getReflectionClass](#getReflectionClass)
  - [getReflectionProperty](#getReflectionProperty)

# Callable

-   #### compose

    Compose functions. Output of one function will be passed as input to
    another function.

    ``` php
    $aToB = fn(int $a): bool => true;
    $bToC = fn(bool $b): string => (string) $b;
    $cTod = fn(string $c): float => (float) $c;

    /** @var callable(int): float $result */
    $result = compose($aToB, $bToC, $cTod);
    ```

-   #### partial and partialLeft

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

-   #### partialRight

    Partial application from last function argument Pass callback and N
    callback arguments. These N arguments will be locked at
    corresponding places (callback parameters) from right-side and new
    callback will be returned with fewer arguments.

    ``` php
    $callback = fn(int $a, string $b, bool $c): bool => true;

    /** @var callable(int): bool $result */
    $result = partialRight($callback, true, "string");
    ```

# Cast

-   #### asArray

    Copy collection as array

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var array<string, int> $result */
    $result = asArray(getCollection());
    ```

-   #### asBool

    Try cast boolean like value. Returns None if cast is not possible

    ``` php
    /** @var Option<bool> $result */
    $result = asBool('yes');
    ```

-   #### asFloat

    Try cast float like value. Returns None if cast is not possible

    ``` php
    /** @var Option<float> $result */
    $result = asFloat('1.1');
    ```

-   #### asInt

    Try cast integer like value. Returns None if cast is not possible

    ``` php
    /** @var Option<int> $result */
    $result = asInt(1);
    ```

-   #### asList

    Copy one or multiple collections as list

    ``` php
    $result = asList([1], ['prop' => 2], [3, 4]); // [1, 2, 3, 4]
    ```

-   #### asNonEmptyArray

    Try cast collection to new non-empty-array. Returns None if there is
    no first collection element

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-array<string, int>> $result */
    $result = asNonEmptyArray(getCollection());
    ```

-   #### asNonEmptyList

    Try cast collection to new non-empty-list. Returns None if there is
    no first collection element

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-list<int>> $result */
    $result = asNonEmptyList(getCollection());
    ```

# Collection

-   #### any

    Returns true if there is collection element which satisfies the
    condition and false otherwise

    ``` php
    any([1, 2, 3], fn(int $value) => $value === 2); // true
    ```

-   #### anyOf

    Returns true if there is collection element of given class and false
    otherwise

    ``` php
    anyOf([new Foo(), 2, 3], Foo::class); // true
    ```

-   #### at

    Find element by it's key

    O(1) for arrays and classes which implement ArrayAccess. O(N) for
    other cases

    ``` php
    /** @var Option<Foo|int> $result */
    $result = at([new Foo(), 2, 3], 1);
    ```

-   #### butLast

    Returns every collection elements except last one

    ``` php
    butLast(['a' => 1, 2, 3]); // ['a' => 1, 2]
    ```

-   #### every

    Returns true if every collection element satisfies the condition and
    false otherwise

    ``` php
    every([1, 2], fn(int $v) => $v === 1); // false
    ```

-   #### everyOf

    Returns true if every collection element is of given class and false
    otherwise

    ``` php
    everyOf([1, new Foo()], Foo::class); // false
    ```

-   #### exists

    Find if there is collection element which satisfies the condition.
    The condition can be an element value or predicate

    ``` php
    exists([1, 2], fn(int $v): bool => $v === 1); // true
    ```

    ``` php
    exists([1, 2], 1); // true
    ```

-   #### filter

    Filter collection by condition. Do not preserve keys by default

    ``` php
    filter([1, 2], fn(int $v): bool => $v === 2); // [2]
    ```

-   #### filterNotNull

    Filter not null elements. Do not preserve keys by default

    ``` php
    filterNotNull([1, null, 2]); // [1, 2]
    ```

-   #### filterOf

    Filter elements of given class. Do not preserve keys by default

    ``` php
    /** @var list<Foo> $result */
    $result = filterOf([1, new Foo(), 2], Foo::class);
    ```

-   #### first

    Find first element which satisfies the condition

    ``` php
    /** @var Option<int> $result */
    $result = first([1, 2], fn(int $v): bool => $v === 2);
    ```

-   #### firstOf

    Find first element of given class

    ``` php
    /** @var Option<Foo> $result */
    $result = firstOf([1, new Foo(1), new Foo(2)], Foo::class);
    ```

-   #### flatMap

    Flat map Consists of map and flatten operations

    ``` php
    /**
     * 1) map [1, 4] to [[0, 1, 2], [3, 4, 5]]
     * 2) flatten [[0, 1, 2], [3, 4, 5]] to [0, 1, 2, 3, 4, 5]
     */
    flatMap([1, 4], fn(int $x) => [$x - 1, $x, $x + 1]); // [0, 1, 2, 3, 4, 5]
    ```

-   #### fold

    Fold many elements into one

    ``` php
    fold(
      '', 
      ['a', 'b', 'c'], 
      fn(string $accumulator, $currentValue) => $accumulator . $currentValue
    ); 

    // 'abc'
    ```

-   #### group

    Group collection elements by key returned by function

    ``` php
    group( 
      [1, 2, 3], 
      fn(int $v): int => $v
    ); 

    // [1 => [1], 2 => [2], 3 => [3]]
    ```

-   #### head

    Returns collection first element

    ``` php
    /** @var Option<int> $result */
    $result = head([1, 2, 3]); 
    ```

-   #### keys

    Returns list of collection keys

    ``` php
    keys(['a' => 1, 'b' => 2]); // ['a', 'b']
    ```

-   #### last

    Returns last collection element and None if there is no last element

    ``` php
    /** @var Option<int> $result */
    $result = last([1, 2, 3]);
    ```

-   #### map

    Produces a new array of elements by mapping each element in
    collection through a transformation function (callback).

    ``` php
    map([1, 2, 3], fn(int $v) => (string) $v); // ['1', '2', '3']
    ```

-   #### partition

    Divide collection by given conditions

    ``` php
    partition(
      ['a' => 1, 'b' => 2],
      fn(int $x) => $x % 2 === 0 
    );

    // [[2], [1]]
    ```

-   #### partitionOf

    Divide collection by given classes

    ``` php
    /** @var array{list<Foo>, list<Bar>, list<Foo|Bar>} $result */
    $result = partitionOf(
      [new Foo(), new Bar()],
      Foo::class,
      Bar::class 
    );
    ```

-   #### pop

    Pop last collection element and return tuple containing this element
    and other collection elements. If there is no last element then
    returns None

    ``` php
    [$head, $tail] = pop([1, 2, 3])->get(); // [3, [1, 2]]
    ```

    ``` php
    Option::do(function () use ($collection) {
      [$head, $tail] = yield pop($collection);
      return doSomethingWithHeadAndTail($head, $tail);
    })   
    ```

-   #### reduce

    Reduce multiple elements into one. Returns None for empty collection

    ``` php
    /** @var Option<string> $option */
    $option = reduce(
      ['a', 'b', 'c'], 
      fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
    ); 

    $option->get(); // 'abc'
    ```

-   #### reindex

    Produces a new array of elements by assigning the values to keys
    generated by a transformation function (callback).

    ``` php
    reindex([1, 'a' => 2], fn (int $value) => $value); // [1 => 1, 2 => 2]
    ```

-   #### reverse

    Copy collection in reversed order

    ``` php
    reverse([1, 2, 3]); // [3, 2, 1]   
    ```

-   #### second

    Returns second collection element. None if there is no second
    collection element

    ``` php
    second([1, 2, 3])->get(); // 2   
    ```

-   #### shift

    Shift first collection element and return tuple containing this
    element and other collection elements. If there is no first element
    then returns None

    ``` php
    [$head, $tail] = shift([1, 2, 3])->get(); // [1, [2, 3]]   
    ```

    ``` php
    Option::do(function () use ($collection) {
      [$head, $tail] = yield shift($collection);
      return doSomethingWithHeadAndTail($head, $tail);
    })   
    ```

-   #### tail

    Returns every collection element except first

    ``` php
    tail([1, 2, 3]); // [2, 3]   
    ```

-   #### zip

    Returns a iterable collection formed from this iterable collection
    and another iterable collection by combining corresponding elements
    in pairs.

    If one of the two collections is longer than the other, its
    remaining elements are ignored.

    ``` php
    zip([1, 2, 3], ['a', 'b']); // [[1, 'a'], [2, 'b']]
    ```

# Evidence

-   #### proveArray

    Prove that given collection is of array type

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<array<string, int>> $result */
    $result = proveArray(getCollection());
    ```

-   #### proveNonEmptyArray

    Prove that given collection is of non-empty-array type

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-array<string, int>> $result */
    $result = proveNonEmptyArray(getCollection());
    ```

-   #### proveArrayOf

    Prove that collection is of array type and every element is of given
    class

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<array<string, Foo>> $result */
    $result = proveArrayOf(getCollection(), Foo::class);
    ```

-   #### proveNonEmptyArrayOf

    Prove that collection is of non-empty-array type and every element
    is of given class

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-array<string, Foo>> $result */
    $result = proveNonEmptyArrayOf(getCollection(), Foo::class);
    ```

-   #### proveList

    Prove that given collection is of list type

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<list<string, int>> $result */
    $result = proveList(getCollection());
    ```

-   #### proveNonEmptyList

    Prove that given collection is of non-empty-list type

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-list<string, int>> $result */
    $result = proveNonEmptyList(getCollection());
    ```

-   #### proveListOf

    Prove that collection is of array type and every element is of given
    class

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<list<string, Foo>> $result */
    $result = proveListOf(getCollection(), Foo::class);
    ```

-   #### proveNonEmptyListOf

    Prove that collection is of non-empty-list type and every element is
    of given class

    ``` php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }

    /** @var Option<non-empty-list<string, Foo>> $result */
    $result = proveNonEmptyListOf(getCollection(), Foo::class);
    ```

-   #### proveBool

    Prove that subject is of boolean type

    ``` php
    /** @var Option<bool> $result */
    $result = proveBool($subject);
    ```

-   #### proveTrue

    Prove that subject is of boolean type and it's value is true

    ``` php
    /** @var Option<true> $result */
    $result = proveTrue($subject);
    ```

-   #### proveFalse

    Prove that subject is of boolean type and it's value is false

    ``` php
    /** @var Option<false> $result */
    $result = proveFalse($subject);
    ```

-   #### proveString

    Prove that subject is of string type

    ``` php
    /** @var Option<string> $result */
    $result = proveString($subject);
    ```

-   #### proveNonEmptyString

    Prove that subject is of given class

    ``` php
    $possiblyEmptyString = '';

    /** @var Option<non-empty-string> $result */
    $result = proveNonEmptyString($possiblyEmptyString);
    ```

-   #### proveCallableString

    Prove that subject is of callable-string type

    ``` php
    /** @var Option<callable-string> $result */
    $result = proveCallableString($subject);
    ```

-   #### proveClassString

    Prove that subject is of class-string type

    ``` php
    /** @var Option<class-string> $result */
    $result = proveClassString($subject);
    ```

-   #### proveFloat

    Prove that subject is of float type

    ``` php
    /** @var Option<float> $result */
    $result = proveFloat($subject);
    ```

-   #### proveInt

    Prove that subject is of int type

    ``` php
    /** @var Option<int> $result */
    $result = proveInt($subject);
    ```

-   #### proveOf

    Prove that subject is of given class

    ``` php
    /** @var Option<Foo> $result */
    $result = proveOf(new Bar(), Foo::class);
    ```

# Json

-   #### jsonDecode

    Decode json string into associative array. Returns Left on error

    ``` php
    jsonDecode('{"a": [{"b": true}]}')->get(); // ['a' => [['b' => true]]] 
    ```

-   #### jsonSearch

    Search by JsonPath expression. Returns None if there is no data by
    given expression. @see jmespath

    ``` php
    jsonSearch('a[0].b', ['a' => [['b' => true]]]); // true
    jsonSearch('a[0].b', '{"a": [{"b": true}]}'); // true
    ```

# Reflection

-   #### getNamedTypes

    Returns property types by property reflection

    ``` php
    $fooProp = new ReflectionProperty(Foo::class, 'a');

    /** @var list<ReflectionNamedType> $result */
    $result = getNamedTypes($fooProp); 
    ```

-   #### getReflectionClass

    Returns class reflection or Left on exception

    ``` php
    /** @var Either<ReflectionException, ReflectionClass> $result */
    $result = getReflectionClass(Foo::class); 
    ```

-   #### getReflectionProperty

    Returns property reflection or Left on exception

    ``` php
    /** @var Either<ReflectionException, ReflectionProperty> $result */
    $result = getReflectionProperty(Foo::class, 'a'); 
    ```
