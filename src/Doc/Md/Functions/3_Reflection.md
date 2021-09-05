# Reflection

- #### getReflectionClass
  Returns class reflection or Left on exception

  ```php
  /** @var Either<ReflectionException, ReflectionClass> $result */
  $result = getReflectionClass(Foo::class); 
  ```


- #### getReflectionProperty
  Returns property reflection or Left on exception

  ```php
  /** @var Either<ReflectionException, ReflectionProperty> $result */
  $result = getReflectionProperty(Foo::class, 'a'); 
  ```

