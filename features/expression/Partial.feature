Feature: Partial
  Scenario: partial left for closure with 3 parameters
    Given expression \Fp\Function\partialLeft(function(int $a, string $b, bool $c): bool {}, 1)
    Then type is pure-Closure(string, bool):bool

  Scenario: partial left for closure with 2 parameters
    Given expression \Fp\Function\partialLeft(function(int $a, string $b): bool {}, 1)
    Then type is pure-Closure(string):bool

  Scenario: partial left for closure with 1 parameters
    Given expression \Fp\Function\partialLeft(function(int $a): bool {}, 1)
    Then type is pure-Closure():bool

  Scenario: partial right for closure with 3 parameters
    Given expression \Fp\Function\partialRight(function(int $a, string $b, bool $c): bool {}, true)
    Then type is pure-Closure(int, string):bool

  Scenario: partial right for closure with 2 parameters
    Given expression \Fp\Function\partialRight(function(int $a, string $b): bool {}, '')
    Then type is pure-Closure(int):bool

  Scenario: partial right for closure with 1 parameters
    Given expression \Fp\Function\partialRight(function(int $a): bool {}, 1)
    Then type is pure-Closure():bool
