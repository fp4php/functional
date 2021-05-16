# Functional PHP
PHP Functional Programming library. Monads and common use functions.


## Documentation
- ### [Functions](doc/Functions.md)
- ### [Monads](doc/Monads.md)


## Installation

### Composer 

```console
$ composer require whsv26/functional
```

### Enable psalm plugins (optional)
To improve type inference for particular functions

```console
$ vendor/bin/psalm-plugin enable Fp\\Psalm\\PartialFunctionReturnTypeProvider
$ vendor/bin/psalm-plugin enable Fp\\Psalm\\PartitionFunctionReturnTypeProvider
$ vendor/bin/psalm-plugin enable Fp\\Psalm\\PluckFunctionReturnTypeProvider
$ vendor/bin/psalm-plugin enable Fp\\Psalm\\OptionGetOrElseMethodReturnTypeProvider
```


## Contribution

### Build documentation
1) Install dependencies
  ```console
  whsv26@whsv26:~$ sudo apt install pandoc
  ```

2) Generate **doc** from **src**
  ```console
  whsv26@whsv26:~$ make
  ```
