Feature: Partition
  Scenario: Partition by 1 predicate
    Given expression:
    """
    \Fp\Function\partition(
      [2, 3, 4, 5],
      fn(int $v) => $v % 2 === 0
    )
    """
    Then type is array{0: array<0|1|2|3, 2|3|4|5>, 1: array<0|1|2|3, 2|3|4|5>}

  Scenario: Partition by 2 predicate
    Given expression:
    """
    \Fp\Function\partition(
      [1],
      fn(int $v) => $v % 2 === 0,
      fn(int $v) => $v % 2 === 1,
    )
    """
    Then type is array{0: array<0, 1>, 1: array<0, 1>, 2: array<0, 1>}
