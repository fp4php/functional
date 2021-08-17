# Functional PHP
PHP Functional Programming library. Monads and common use functions.

![psalm level](https://shepherd.dev/github/whsv26/functional/level.svg)
![psalm type coverage](https://shepherd.dev/github/whsv26/functional/coverage.svg)
[![phpunit coverage](https://coveralls.io/repos/github/whsv26/functional/badge.svg)](https://coveralls.io/github/whsv26/functional)

## Documentation
- ### [Collections](doc/Collections.md)
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
