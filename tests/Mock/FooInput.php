<?php

declare(strict_types=1);

namespace Tests\Mock;

/**
 * @internal
 */
class FooInput {
    public function __construct(
        public int $a,
        public string $b,
        public bool $c,
    )
    {
    }
}
