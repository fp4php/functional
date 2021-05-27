# Collection

- #### any
  Returns true if there is collection element which satisfies the condition and false otherwise

  ```php
  any([1, 2, 3], fn(int $value) => $value === 2); // true
  ```

- #### anyOf
  Returns true if there is collection element of given class and false otherwise

  ```php
  anyOf([new Foo(), 2, 3], Foo::class); // true
  ```

- #### at
  Find element by it's key

  O(1) for arrays and classes which implement ArrayAccess. O(N) for other cases

  ```php
  /** @var Option<Foo|int> $result */
  $result = at([new Foo(), 2, 3], 1);
  ```

- #### butLast
  Returns every collection elements except last one

  ```php
  butLast(['a' => 1, 2, 3]); // ['a' => 1, 2]
  ```

- #### every
  Returns true if every collection element satisfies the condition and false otherwise

  ```php
  every([1, 2], fn(int $v) => $v === 1); // false
  ```
  
- #### everyOf
  Returns true if every collection element is of given class and false otherwise

  ```php
  everyOf([1, new Foo()], Foo::class); // false
  ```

- #### exists
  Find if there is collection element which satisfies the condition.
  The condition can be an element value or predicate

  ```php
  exists([1, 2], fn(int $v): bool => $v === 1); // true
  ```

  ```php
  exists([1, 2], 1); // true
  ```

- #### filter
  Filter collection by condition. Do not preserve keys by default

  ```php
  filter([1, 2], fn(int $v): bool => $v === 2); // [2]
  ```

- #### filterNotNull
  Filter not null elements. Do not preserve keys by default

  ```php
  filterNotNull([1, null, 2]); // [1, 2]
  ```

- #### filterOf
  Filter elements of given class. Do not preserve keys by default

  ```php
  /** @var list<Foo> $result */
  $result = filterOf([1, new Foo(), 2], Foo::class);
  ```

- #### first
  Find first element which satisfies the condition

  ```php
  /** @var Option<int> $result */
  $result = first([1, 2], fn(int $v): bool => $v === 2);
  ```

- #### firstOf
  Find first element of given class

  ```php
  /** @var Option<Foo> $result */
  $result = firstOf([1, new Foo(1), new Foo(2)], Foo::class);
  ```

- #### flatMap
  Flat map Consists of map and flatten operations

  ```php
  /**
   * 1) map [1, 4] to [[0, 1, 2], [3, 4, 5]]
   * 2) flatten [[0, 1, 2], [3, 4, 5]] to [0, 1, 2, 3, 4, 5]
   */
  flatMap([1, 4], fn(int $x) => [$x - 1, $x, $x + 1]); // [0, 1, 2, 3, 4, 5]
  ```

- #### fold
  Fold many elements into one

  ```php
  fold(
    '', 
    ['a', 'b', 'c'], 
    fn(string $accumulator, $currentValue) => $accumulator . $currentValue
  ); 
  
  // 'abc'
  ```

- #### group
  Group collection elements by key returned by function

  ```php
  group( 
    [1, 2, 3], 
    fn(int $v): int => $v
  ); 
  
  // [1 => [1], 2 => [2], 3 => [3]]
  ```

- #### head
  Returns collection first element

  ```php
  /** @var Option<int> $result */
  $result = head([1, 2, 3]); 
  ```

- #### isSequence
  Check if collection is ascending or descending integer sequence from given start value

  ```php
  isSequence([1, 2, 3]); // false
  isSequence([0, 1, 2, 3]); // true 
  isSequence([]); // true 
  ```

- #### isNonEmptySequence
  Check if collection is non empty ascending or descending integer sequence from given start value

  ```php
  isNonEmptySequence([1, 2, 3]); // false
  isNonEmptySequence([0, 1, 2, 3]); // true
  isNonEmptySequence([]); // false 
  ```

- #### keys
  Returns list of collection keys

  ```php
  keys(['a' => 1, 'b' => 2]); // ['a', 'b']
  ```

- #### last
  Returns last collection element and None if there is no last element

  ```php
  /** @var Option<int> $result */
  $result = last([1, 2, 3]);
  ```

- #### map
  Produces a new array of elements by mapping each element in collection through a transformation function (callback).

  ```php
  map([1, 2, 3], fn(int $v) => (string) $v); // ['1', '2', '3']
  ```

- #### partition
  Divide collection by given conditions

  ```php
  partition(
    ['a' => 1, 'b' => 2],
    fn(int $x) => $x % 2 === 0 
  );
  
  // [[2], [1]]
  ```

- #### partitionOf
  Divide collection by given classes

  ```php
  /** @var array{list<Foo>, list<Bar>, list<Foo|Bar>} $result */
  $result = partitionOf(
    [new Foo(), new Bar()],
    Foo::class,
    Bar::class 
  );
  ```

- #### pluck
  Transform every collection element into given property or key value

  ```php
  pluck([['a' => 1], ['a' => 2]], 'a'); // [1, 2]   
  ```

- #### pop
  Pop last collection element and return tuple containing this element and other collection elements. If there is no last element then returns None

  ```php
  [$head, $tail] = pop([1, 2, 3])->get(); // [3, [1, 2]]
  ```
  
  ```php
  Option::do(function () use ($collection) {
    [$head, $tail] = yield pop($collection);
    return doSomethingWithHeadAndTail($head, $tail);
  })   
  ```

- #### reduce
  Reduce multiple elements into one. Returns None for empty collection

  ```php
  /** @var Option<string> $option */
  $option = reduce(
    ['a', 'b', 'c'], 
    fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
  ); 
  
  $option->get(); // 'abc'
  ```

- #### reduceNel
  Reduce non-empty-list into one value

  ```php
  reduceNel(
    ['a', 'b', 'c'], 
    fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
  ); 
  
  // 'abc'
  ```

- #### reduceNer
  Reduce non-empty-array into one value

  ```php
  reduceNer(
    ['x' => 'a', 'b', 'c'], 
    fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
  ); 
  
  // 'abc'
  ```

- #### reindex
  Produces a new array of elements by assigning the values to keys generated by a transformation function (callback).

  ```php
  reindex([1, 'a' => 2], fn (int $value) => $value); // [1 => 1, 2 => 2]
  ```

- #### reverse
  Copy collection in reversed order

  ```php
  reverse([1, 2, 3]); // [3, 2, 1]   
  ```

- #### second
  Returns second collection element. None if there is no second collection element

  ```php
  second([1, 2, 3])->get(); // 2   
  ```

- #### shift
  Shift first collection element and return tuple containing this element and other collection elements. If there is no first element then returns None

  ```php
  [$head, $tail] = shift([1, 2, 3])->get(); // [1, [2, 3]]   
  ```

  ```php
  Option::do(function () use ($collection) {
    [$head, $tail] = yield shift($collection);
    return doSomethingWithHeadAndTail($head, $tail);
  })   
  ```

- #### tail
  Returns every collection element except first

  ```php
  tail([1, 2, 3]); // [2, 3]   
  ```

- #### unique
  Returns unique collection elements

  ```php
  unique([1, 2, 2, 3, 3, 3, 3]); // [1, 2, 3]   
  ```

  ```php
  unique($users, fn(User $user) => $user->getIdAsString());   
  ```

- #### zip
  Returns a iterable collection formed from this iterable collection and another iterable collection by combining corresponding elements in pairs.

  If one of the two collections is longer than the other, its remaining elements are ignored.

  ```php
  zip([1, 2, 3], ['a', 'b']); // [[1, 'a'], [2, 'b']]
  ```

