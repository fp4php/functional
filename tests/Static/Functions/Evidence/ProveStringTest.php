<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Tests\PhpBlockTestCase;

final class ProveStringTest extends PhpBlockTestCase
{
    public function testProveNonEmptyString(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                $result = \Fp\Evidence\proveNonEmptyString("")->get();
            ',
            'non-empty-string|null'
        );
    }
}
