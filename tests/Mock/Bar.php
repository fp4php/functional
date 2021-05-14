<?php

declare(strict_types=1);

namespace Tests\Mock;

class Bar {
    public function __construct(public bool|int $a)
    {
    }
}
