# Reflection

- #### getNamedTypes
  Returns property types by property reflection

  ```php
  $fooProp = new ReflectionProperty(Foo::class, 'a');
  
  /** @var list<ReflectionNamedType> $result */
  $result = getNamedTypes($fooProp); 
  ```

- #### getReflectionProperty
  Returns property reflection or Left on exception

  ```php
  /** @var Either<ReflectionException, ReflectionProperty> $result */
  $result = getReflectionProperty(Foo::class, 'a'); 
  ```



- #### getReflectionClass
  Returns class reflection or Left on exception

  ```php
  /** @var Either<ReflectionException, ReflectionClass> $result */
  $result = getReflectionClass(Foo::class); 
  ```

