<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use function Fp\Evidence\proveNonEmptyString;

final class ProveStringStaticTest
{
    /**
     * @return non-empty-string|null
     */
    public function testProveNonEmptyString(): ?string
    {
        return proveNonEmptyString("")->get();
    }
}
