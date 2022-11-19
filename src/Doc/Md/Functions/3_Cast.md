# Cast
- #### asArray
  Copy collection as array

  ```php
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
  
  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asBool;
  
  /** @var Option<bool> $result */
  $result = asBool('yes');
  ```

- #### asFloat
  Try cast float like value. Returns None if cast is not possible

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asFloat;
  
  /** @var Option<float> $result */
  $result = asFloat('1.1');
  ```

- #### asInt
  Try cast integer like value. Returns None if cast is not possible

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asInt;
  /** @var Option<int> $result */
  $result = asInt(1);
  ```

- #### asList
  Copy one or multiple collections as list

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asList;
  
  $result = asList([1], ['prop' => 2], [3, 4]); // [1, 2, 3, 4]
  ```

- #### asNonEmptyArray
  Try cast collection to new non-empty-array. Returns None if there is no first collection element

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asNonEmptyArray;
  
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }

  /** @var Option<non-empty-array<string, int>> $result */
  $result = asNonEmptyArray(getCollection());
  ```

- #### asNonEmptyList
  Try cast collection to new non-empty-list. Returns None if there is no first collection element

  ```php
  <?php
  
  declare(strict_types=1);
  
  use function Fp\Cast\asNonEmptyList;
  
  /** @psalm-return iterable<string, int> */
  function getCollection(): array { return []; }

  /** @var Option<non-empty-list<int>> $result */
  $result = asNonEmptyList(getCollection());
  ```
