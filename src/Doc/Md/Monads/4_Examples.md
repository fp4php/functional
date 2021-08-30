# Examples

- #### Filter chaining
  ```php
      /**
       * @psalm-return Option<Union>
       */
      function getUnionTypeParam(Union $union): Option
      {
          return Option::do(function () use ($union) {
              $atomics = $union->getAtomicTypes();
              yield proveTrue(1 === count($atomics));
              $atomic = yield head($atomics);
  
              return yield self::filterTIterableValueTypeParam($atomic)
                  ->orElse(fn() => self::filterTArrayValueTypeParam($atomic))
                  ->orElse(fn() => self::filterTListValueTypeParam($atomic))
                  ->orElse(fn() => self::filterTGenericObjectValueTypeParam($atomic))
                  ->orElse(fn() => self::filterTKeyedArrayValueTypeParam($atomic));
          });
      }
  
      /**
       * @psalm-return Option<Union>
       */
      function filterTIterableTypeParam(Atomic $atomic): Option
      {
          return Option::some($atomic)
              ->filter(fn(Atomic $a) => $a instanceof TIterable)
              ->map(fn(TIterable $a) => $a->type_params[1]);
      }
  
      /**
       * @psalm-return Option<Union>
       */
      function filterTArrayTypeParam(Atomic $atomic): Option
      {
          return Option::some($atomic)
              ->filter(fn(Atomic $a) => $a instanceof TArray)
              ->map(fn(TArray $a) => $a->type_params[1]);
      }
  
      /**
       * @psalm-return Option<Union>
       */
      function filterTListTypeParam(Atomic $atomic): Option
      {
          return Option::some($atomic)
              ->filter(fn(Atomic $a) => $a instanceof TList)
              ->map(fn(TList $a) => $a->type_param);
      }
  
      /**
       * @psalm-return Option<Union>
       */
      function filterTKeyedArrayTypeParam(Atomic $atomic): Option
      {
          return Option::some($atomic)
              ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
              ->map(fn(TKeyedArray $a) => $a->getGenericValueType());
      }
  
      /**
       * @psalm-return Option<Union>
       */
      function filterTGenericObjectTypeParam(Atomic $atomic): Option
      {
          return Option::some($atomic)
              ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
              ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                  is_a($a->value, Seq::class, true) => $a->type_params[0],
                  is_a($a->value, Set::class, true) => $a->type_params[0],
                  is_a($a->value, Map::class, true) => $a->type_params[1],
                  is_a($a->value, NonEmptySeq::class, true) => $a->type_params[0],
                  is_a($a->value, NonEmptySet::class, true) => $a->type_params[0],
                  default => null
              }));
      }
  ```

