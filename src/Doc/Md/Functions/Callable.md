# Callable
- #### compose
  Compose functions. 
  Output of one function will be passed as input to another function.
  
  ```php
  $aToB = fn(int $a): bool => true;
  $bToC = fn(bool $b): string => (string) $b;
  $cTod = fn(string $c): float => (float) $c;
  
  /** @var callable(int): float $result */
  $result = compose($aToB, $bToC, $cTod);
  ```

- #### partial and partialLeft
  Partial application from first function argument.
  Pass callback and N callback arguments. 
  These N arguments will be locked at corresponding places (callback parameters) from left-side and new callback will be returned with fewer arguments.   

  ```php
  $callback = fn(int $a, string $b, bool $c): bool => true;
  
  /** @var callable(bool): bool $result */
  $result = partial($callback, 1, "string");
  
  /** @var callable(bool): bool $result */
  $result = partialLeft($callback, 1, "string");
  ```

- #### partialRight
  Partial application from last function argument
  Pass callback and N callback arguments.
  These N arguments will be locked at corresponding places (callback parameters) from right-side and new callback will be returned with fewer arguments.


  ```php
  $callback = fn(int $a, string $b, bool $c): bool => true;
  
  /** @var callable(int): bool $result */
  $result = partialRight($callback, true, "string");
  ```
