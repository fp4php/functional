<?php

declare(strict_types=1);

namespace Fp\Collections;

interface HashContract
{
    public function equals(mixed $that): bool;
    public function hashCode(): string;
}
