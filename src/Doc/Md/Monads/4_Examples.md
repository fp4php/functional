# Examples

- #### Type assertions with Option
Type assertions with Option monad via [PHP generators](https://www.php.net/manual/en/language.generators.syntax.php) based [do-notation](https://en.wikibooks.org/wiki/Haskell/do_notation) implementation
  ```php
  /**
   * Inferred type is Option<Foo> 
   */ 
  $maybeFooMaybeNot = Option::do(function() use ($untrusted) {
      $notNull = yield Option::fromNullable($untrusted);
      yield proveTrue(is_array($notNull)); // Inferred type is array<array-key, mixed> 
      $list = yield proveList($notNull); // Inferred type is list<mixed>
      $nonEmptyList = yield proveNonEmptyList($list); // Inferred type is non-empty-list<mixed>
      $nonEmptyListOfFoo = yield proveNonEmptyListOf($nonEmptyList, Foo::class); // Inferred type is non-empty-list<Foo>
      $firstFoo = $nonEmptyListOfFoo[0]; // Inferred type is Foo
  
      return $firstFoo; // I'm sure it's Foo object
  });
  
  /**
   * Inferred type is Foo
   */
  $foo = $maybeFooMaybeNot->getOrCall(fn() => new Foo(0))
  ```

- #### Filter chaining
Build complex filters with small Option-based blocks
  ```php
  /**
   * @return Option<Union>
   */
  function getUnionTypeParam(Union $union): Option
  {
      return Option::do(function () use ($union) {
          $atomics = $union->getAtomicTypes();
          yield proveTrue(1 === count($atomics));
          $atomic = yield head($atomics);
  
          return yield self::filterTIterableTypeParam($atomic)
              ->orElse(fn() => self::filterTArrayTypeParam($atomic))
              ->orElse(fn() => self::filterTListTypeParam($atomic))
              ->orElse(fn() => self::filterTGenericObjectTypeParam($atomic))
              ->orElse(fn() => self::filterTKeyedArrayTypeParam($atomic));
      });
  }
  
  /**
   * @return Option<Union>
   */
  function filterTIterableTypeParam(Atomic $atomic): Option
  {
      return Option::some($atomic)
          ->filter(fn(Atomic $a) => $a instanceof TIterable)
          ->map(fn(TIterable $a) => $a->type_params[1]);
  }
  
  /**
   * @return Option<Union>
   */
  function filterTArrayTypeParam(Atomic $atomic): Option
  {
      return Option::some($atomic)
          ->filter(fn(Atomic $a) => $a instanceof TArray)
          ->map(fn(TArray $a) => $a->type_params[1]);
  }
  
  /**
   * @return Option<Union>
   */
  function filterTListTypeParam(Atomic $atomic): Option
  {
      return Option::some($atomic)
          ->filter(fn(Atomic $a) => $a instanceof TList)
          ->map(fn(TList $a) => $a->type_param);
  }
  
  /**
   * @return Option<Union>
   */
  function filterTKeyedArrayTypeParam(Atomic $atomic): Option
  {
      return Option::some($atomic)
          ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
          ->map(fn(TKeyedArray $a) => $a->getGenericValueType());
  }
  
  /**
   * @return Option<Union>
   */
  function filterTGenericObjectTypeParam(Atomic $atomic): Option
  {
      return Option::some($atomic)
          ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
          ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
              classOf($a->value, Seq::class) => $a->type_params[0],
              classOf($a->value, Set::class) => $a->type_params[0],
              classOf($a->value, Map::class) => $a->type_params[1],
              classOf($a->value, NonEmptySeq::class) => $a->type_params[0],
              classOf($a->value, NonEmptySet::class) => $a->type_params[0],
              default => null
          }));
  }
  ```

