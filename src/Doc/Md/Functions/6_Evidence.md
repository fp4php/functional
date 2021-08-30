# Evidence
- #### proveArray
  Prove that given collection is of array type

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<array<string, int>> $result */
  $result = proveArray(getCollection());
  ```

- #### proveNonEmptyArray
  Prove that given collection is of non-empty-array type

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<non-empty-array<string, int>> $result */
  $result = proveNonEmptyArray(getCollection());
  ```

- #### proveArrayOf
  Prove that collection is of array type and every element is of given class

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<array<string, Foo>> $result */
  $result = proveArrayOf(getCollection(), Foo::class);
  ```

- #### proveNonEmptyArrayOf
  Prove that collection is of non-empty-array type and every element is of given class

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<non-empty-array<string, Foo>> $result */
  $result = proveNonEmptyArrayOf(getCollection(), Foo::class);
  ```

- #### proveList
  Prove that given collection is of list type

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<list<string, int>> $result */
  $result = proveList(getCollection());
  ```

- #### proveNonEmptyList
  Prove that given collection is of non-empty-list type

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<non-empty-list<string, int>> $result */
  $result = proveNonEmptyList(getCollection());
  ```

- #### proveListOf
  Prove that collection is of array type and every element is of given class

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<list<string, Foo>> $result */
  $result = proveListOf(getCollection(), Foo::class);
  ```

- #### proveNonEmptyListOf
  Prove that collection is of non-empty-list type and every element is of given class

  ```php
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }
  
  /** @var Option<non-empty-list<string, Foo>> $result */
  $result = proveNonEmptyListOf(getCollection(), Foo::class);
  ```

- #### proveBool
  Prove that subject is of boolean type

  ```php
  /** @var Option<bool> $result */
  $result = proveBool($subject);
  ```

- #### proveTrue
  Prove that subject is of boolean type and it's value is true

  ```php
  /** @var Option<true> $result */
  $result = proveTrue($subject);
  ```

- #### proveFalse
  Prove that subject is of boolean type and it's value is false

  ```php
  /** @var Option<false> $result */
  $result = proveFalse($subject);
  ```


- #### proveString
  Prove that subject is of string type

  ```php
  /** @var Option<string> $result */
  $result = proveString($subject);
  ```

- #### proveNonEmptyString
  Prove that subject is of given class

  ```php
  $possiblyEmptyString = '';
  
  /** @var Option<non-empty-string> $result */
  $result = proveNonEmptyString($possiblyEmptyString);
  ```

- #### proveCallableString
  Prove that subject is of callable-string type

  ```php
  /** @var Option<callable-string> $result */
  $result = proveCallableString($subject);
  ```

- #### proveClassString
  Prove that subject is of class-string type

  ```php
  /** @var Option<class-string> $result */
  $result = proveClassString($subject);
  ```

- #### proveFloat
  Prove that subject is of float type

  ```php
  /** @var Option<float> $result */
  $result = proveFloat($subject);
  ```

- #### proveInt
  Prove that subject is of int type

  ```php
  /** @var Option<int> $result */
  $result = proveInt($subject);
  ```

- #### proveOf
  Prove that subject is of given class

  ```php
  /** @var Option<Foo> $result */
  $result = proveOf(new Bar(), Foo::class);
  ```







