# Callable
- #### compose
  Compose functions. Output of one function will be passed as input to another function
  
  ```php
  $aToB = fn(int $a): bool => true;
  $bToC = fn(bool $b): string => (string) $b;
  $cTod = fn(string $c): float => (float) $c;
  
  /** @var callable(int): float $result */
  $result = \Fp\Callable\compose($aToB, $bToC, $cTod);
  ```

- #### partial and partialLeft
  Partial application from first function argument

  ```php
  $callback = fn(int $a, string $b, bool $c): bool => true;
  
  /** @var callable(bool): bool $result */
  $result = \Fp\Callable\partial($callback, 1, "string");
  
  /** @var callable(bool): bool $result */
  $result = \Fp\Callable\partialLeft($callback, 1, "string");
  ```

- #### partialRight
  Partial application from last function argument

  ```php
  $callback = fn(int $a, string $b, bool $c): bool => true;
  
  /** @var callable(int): bool $result */
  $result = \Fp\Callable\partialRight($callback, true, "string");
  ```
