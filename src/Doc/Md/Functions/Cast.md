# Cast
- #### asArray
    Copy collection as array
  
    ```php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
    
    /** @var array<string, int> $result */
    $result = asArray(getCollection());
    ```

- #### asBool
  Try cast boolean like value
  
    Returns None if cast is not possible
  
    ```php
    /** @var Option<bool> $result */
    $result = asBool('yes');
    ```

- #### asFloat
  Try cast float like value
  
  Returns None if cast is not possible

    ```php
    /** @var Option<float> $result */
    $result = asFloat('1.1');
    ```

- #### asInt
  Try cast integer like value

  Returns None if cast is not possible

    ```php
    /** @var Option<int> $result */
    $result = asInt(1);
    ```

- #### asList
    Copy one or multiple collections as list

    ```php
    $result = asList([1], ['prop' => 2], [3, 4]); // [1, 2, 3, 4]
    ```

- #### asNonEmptyArray
  Try copy and cast collection to non-empty-array
  
  Returns None if there is no first collection element

    ```php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
  
    /** @var Option<non-empty-array<string, int>> $result */
    $result = asNonEmptyArray(getCollection());
    ```

- #### asNonEmptyList
  Try copy and cast collection to non-empty-list

  Returns None if there is no first collection element

    ```php
    /** @psalm-return iterable<string, int> */
    function getCollection(): array { return []; }
  
    /** @var Option<non-empty-list<int>> $result */
    $result = asNonEmptyList(getCollection());
    ```
