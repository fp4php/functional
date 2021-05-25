# Collection

- #### any
  Returns true if there is collection element which satisfies the condition and false otherwise

  ```php
  any(
    [1, 2, 3],
    fn(int $value): bool => $value === 2 
  ); // true
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


- #### copyCollection
  Copy any iterable collection into php array

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var array<string, int> $result */
  $result = copyCollection(getCollection);
  ```


- #### every
  Returns true if every collection element satisfies the condition and false otherwise

  ```php
  every([1, 2], fn(int $v): bool => $v === 1); // false
  ```


- #### everyOf
  Returns true if every collection element is of given class false otherwise

  ```php
  everyOf([1, new Foo()], Foo::class); // false
  ```


- #### filter
  Filter collection by condition. Preserves keys by default

  ```php
  filter([1, 2], fn(int $v): bool => $v === 2); // [1 => 2]
  ```


- #### filterNotNull
  Filter not null elements. Preserves keys by default

  ```php
  filterNotNull([1, null, 2]); // [0 => 1, 2 => 2]
  ```



- #### filterOf
  Filter elements of given class. Preserves keys by default

  ```php
  /** @var array<0|1|2, Foo> $result */
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
  Map collection values into new collection. Keys are preserved

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
  
  // [['b' => 2], ['a' => 1]]
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
  
  // [[$foo], [$bar], []]
  ```



- #### pluck
  Map every collection element into given property/key value

  ```php
  pluck([['a' => 1], ['a' => 2]], 'a'); // [1, 2]   
  ```



- #### pop
  Pop last collection element and return tuple containing this element and other collection elements. If there is no last element then returns None

  ```php
  [$head, $tail] = pop([[1, 2, 3]]); // [3, [1, 2]]   
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





- #### reverse
  Copy collection in reversed order

  ```php
  reverse([[1, 2, 3]]); // [3, 2, 1]   
  ```



- #### second
  Returns second collection element. None if there is no second collection element

  ```php
  second([[1, 2, 3]])->get(); // 2   
  ```


- #### shift
  Shift first collection element and return tuple containing this element and other collection elements. If there is no first element then returns None

  ```php
  [$head, $tail] = shift([[1, 2, 3]]); // [1, [2, 3]]   
  ```




- #### tail
  Returns every collection element except first

  ```php
  tail([[1, 2, 3]]); // [2, 3]   
  ```


- #### unique
  Returns unique collection elements

  ```php
  unique([[1, 2, 2, 3, 3, 3, 3]]); // [1, 2, 3]   
  ```

- #### reindex
  Produces a new array of elements by assigning the values to keys generated by a transformation function (callback).

  ```php
  reindex([1, 'a' => 2], fn (int $value) => $value); // [1 => 1, 2 => 2]
  ```

- #### zip
  Produces a new array of elements by assigning the values to keys generated by a transformation function (callback).

  ```php
  zip([1, 2, 3], ['a', 'b']); // [[1, 'a'], [2, 'b']]
  ```



