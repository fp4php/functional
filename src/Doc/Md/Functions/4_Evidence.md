# Evidence
- #### proveArray
  Prove that given value is of array type

  ```php
  function getMixed(): mixed { return []; }
  
  // inferred as Option<array<array-key, mixed>>
  $result = proveArray(getMixed());
  ```

  Type params from any iterable type will be preserved:

  ```php
  /** @return iterable<string, int> */
  function getCollection(): iterable { return []; }

  // inferred as Option<array<string, int>>
  $result = proveArray(getCollection());
  ```

  Key and value type can be proved separately:

  ```php
  /** @return iterable<mixed, mixed> */
  function getCollection(): iterable { return []; }

  // inferred as Option<array<string, int>>
  $result = proveArray(getCollection(), proveString(...), proveInt(...));
  ```

- #### proveNonEmptyArray
  Prove that given collection is of non-empty-array type

  ```php
  /** @return iterable<string, int> */
  function getCollection(): array { return []; }
  
  // Inferred as Option<non-empty-array<string, int>>
  $result = proveNonEmptyArray(getCollection());
  ```

  Type params from any iterable type will be preserved:

  ```php
  /** @return iterable<string, int> */
  function getCollection(): iterable { return []; }

  // inferred as Option<non-empty-array<string, int>>
  $result = proveNonEmptyArray(getCollection());
  ```

  Key and value type can be proved separately:

  ```php
  /** @return iterable<mixed, mixed> */
  function getCollection(): iterable { return []; }

  // inferred as Option<non-empty-array<string, int>>
  $result = proveNonEmptyArray(getCollection(), proveString(...), proveInt(...));

- #### proveArrayOf
  Prove that collection is of array type and every element is of given class

  ```php
  /** @return iterable<string, int> */
  function getCollection(): array { return []; }
  
  // Inferred as Option<array<string, Foo>>
  $result = proveArrayOf(getCollection(), Foo::class);
  ```

- #### proveNonEmptyArrayOf
  Prove that collection is of non-empty-array type and every element is of given class

  ```php
  /** @return iterable<string, int> */
  function getCollection(): array { return []; }
  
  // Inferred as Option<non-empty-array<string, Foo>>
  $result = proveNonEmptyArrayOf(getCollection(), Foo::class);
  ```

- #### proveList
  Prove that given value is of list type

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<list<mixed>>
  $result = proveList(getMixed());
  ```
  
  Type params from any iterable type will be preserved:

  ```php
  /** @return iterable<int, string> */
  function getCollection(): iterable { return []; }

  // inferred as Option<list<string>>
  $result = proveList(getCollection());
  ```

  Value type can be proved separately:

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<list<int>>
  $result = proveList(getMixed(), proveInt(...));
  ```

- #### proveNonEmptyList
  Prove that given value is of non-empty-list type

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<non-empty-list<mixed>>
  $result = proveNonEmptyList(getMixed());
  ```

  Type params from any iterable type will be preserved:

  ```php
  /** @return iterable<int, string> */
  function getCollection(): iterable { return []; }

  // Inferred as Option<non-empty-list<string>>
  $result = proveNonEmptyList(getCollection());
  ```

  Value type can be proved separately:

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<non-empty-list<int>>
  $result = proveNonEmptyList(getMixed(), proveInt(...));
  ```

- #### proveListOf
  Prove that collection is of array type and every element is of given class

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<list<Foo>>
  $result = proveListOf(getMixed(), Foo::class);
  ```

- #### proveNonEmptyListOf
  Prove that collection is of non-empty-list type and every element is of given class

  ```php
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<non-empty-list<Foo>>
  $result = proveNonEmptyListOf(getMixed(), Foo::class);
  ```

- #### proveBool
  Prove that subject is of boolean type

  ```php
  // Inferred as Option<bool>
  $result = proveBool($subject);
  ```

- #### proveTrue
  Prove that subject is of boolean type, and it's value is true

  ```php
  // Inferred as Option<true>
  $result = proveTrue($subject);
  ```

- #### proveFalse
  Prove that subject is of boolean type, and it's value is false

  ```php
  // Inferred as Option<false>
  $result = proveFalse($subject);
  ```


- #### proveString
  Prove that subject is of string type

  ```php
  // Inferred as Option<string>
  $result = proveString($subject);
  ```

- #### proveNonEmptyString
  Prove that subject is of given class

  ```php
  $possiblyEmptyString = '';
  
  // Inferred as Option<non-empty-string>
  $result = proveNonEmptyString($possiblyEmptyString);
  ```

- #### proveCallableString
  Prove that subject is of callable-string type

  ```php
  // Inferred as Option<callable-string>
  $result = proveCallableString($subject);
  ```

- #### proveClassString
  Prove that subject is of class-string type

  ```php
  // Inferred as Option<class-string>
  $result = proveClassString($subject);
  ```

- #### proveClassStringOf
  Prove that subject is subtype of given class-string

  ```php
  // Inferred as Option<class-string<Collection>>
  $result = proveClassStringOf(ArrayList::class, Collection::class);
  ```

- #### proveFloat
  Prove that subject is of float type

  ```php
  // Inferred as Option<float>
  $result = proveFloat($subject);
  ```

- #### proveInt
  Prove that subject is of int type

  ```php
  // Inferred as Option<int>
  $result = proveInt($subject);
  ```

- #### proveOf
  Prove that subject is of given class

  ```php
  // Inferred as Option<Foo>
  $result = proveOf(new Bar(), Foo::class);
  ```
