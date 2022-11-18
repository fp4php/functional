# Evidence
- #### proveArray
  Prove that given value is of array type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveArray;
  
  function getMixed(): mixed { return []; }
  
  // inferred as Option<array<array-key, mixed>>
  $result = proveArray(getMixed());
  ```

  Type params from any iterable type will be preserved:

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveArray;
  
  /** @return iterable<string, int> */
  function getCollection(): iterable { return []; }

  // inferred as Option<array<string, int>>
  $result = proveArray(getCollection());
  ```

  Key and value type can be proved separately:

  ```php
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

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveNonEmptyArray;
  
  /** @return iterable<string, int> */
  function getCollection(): array { return []; }
  
  // Inferred as Option<non-empty-array<string, int>>
  $result = proveNonEmptyArray(getCollection());
  ```

  Type params from any iterable type will be preserved:

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveNonEmptyArray;
  
  /** @return iterable<string, int> */
  function getCollection(): iterable { return []; }

  // inferred as Option<non-empty-array<string, int>>
  $result = proveNonEmptyArray(getCollection());
  ```

  Key and value type can be proved separately:

  ```php
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

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveList;
  
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<list<mixed>>
  $result = proveList(getMixed());
  ```
  
  Type params from any iterable type will be preserved:

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveList;
  
  /** @return iterable<int, string> */
  function getCollection(): iterable { return []; }

  // inferred as Option<list<string>>
  $result = proveList(getCollection());
  ```

  Value type can be proved separately:

  ```php
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

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveNonEmptyList;
  
  function getMixed(): mixed { return []; }
  
  // Inferred as Option<non-empty-list<mixed>>
  $result = proveNonEmptyList(getMixed());
  ```

  Type params from any iterable type will be preserved:

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveNonEmptyList;
  
  /** @return iterable<int, string> */
  function getCollection(): iterable { return []; }

  // Inferred as Option<non-empty-list<string>>
  $result = proveNonEmptyList(getCollection());
  ```

  Value type can be proved separately:

  ```php
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

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveBool;
  
  // Inferred as Option<bool>
  $result = proveBool($subject);
  ```

- #### proveTrue
  Prove that subject is of boolean type, and it's value is true

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveTrue;
  
  // Inferred as Option<true>
  $result = proveTrue($subject);
  ```

- #### proveFalse
  Prove that subject is of boolean type, and it's value is false

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveFalse;
  
  // Inferred as Option<false>
  $result = proveFalse($subject);
  ```


- #### proveString
  Prove that subject is of string type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveString;
  
  // Inferred as Option<string>
  $result = proveString($subject);
  ```

- #### proveNonEmptyString
  Prove that subject is of given class

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveNonEmptyString;
  
  $possiblyEmptyString = '';
  
  // Inferred as Option<non-empty-string>
  $result = proveNonEmptyString($possiblyEmptyString);
  ```

- #### proveCallableString
  Prove that subject is of callable-string type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveCallableString;
  
  // Inferred as Option<callable-string>
  $result = proveCallableString($subject);
  ```

- #### proveClassString
  Prove that subject is of class-string type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveClassString;
  
  // Inferred as Option<class-string>
  $result = proveClassString($subject);
  ```

- #### proveClassStringOf
  Prove that subject is subtype of given class-string

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveClassStringOf;
  
  // Inferred as Option<class-string<Collection>>
  $result = proveClassStringOf(ArrayList::class, Collection::class);
  ```

- #### proveFloat
  Prove that subject is of float type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveFloat;
  
  // Inferred as Option<float>
  $result = proveFloat($subject);
  ```

- #### proveInt
  Prove that subject is of int type

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveInt;
  
  // Inferred as Option<int>
  $result = proveInt($subject);
  ```

- #### proveOf
  Prove that subject is of given class

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Evidence\proveOf;
  
  // Inferred as Option<Foo>
  $result = proveOf(new Bar(), Foo::class);
  ```
