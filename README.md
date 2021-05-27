# Functional PHP
PHP Functional Programming library. Monads and common use functions.

![badges](https://shepherd.dev/github/whsv26/functional/coverage.svg)
![badges](https://shepherd.dev/github/whsv26/functional/level.svg)
![badges](phpunit-coverage.svg)

## Documentation
- ### [Functions](doc/Functions.md)
- ### [Monads](doc/Monads.md)


## Installation

### Composer 

```console
$ composer require whsv26/functional
```

### Enable psalm plugin (optional)
To improve type inference for particular functions

```console
$ vendor/bin/psalm-plugin enable Fp\\Psalm\\FunctionalPlugin
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
