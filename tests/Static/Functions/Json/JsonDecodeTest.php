<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Json;

use Fp\Functional\Either\Either;
use Tests\PhpBlockTestCase;

final class JsonDecodeTest extends PhpBlockTestCase
{
    public function testDecode(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $json = "{}";
            $result = \Fp\Json\jsonDecode($json);
        ';

        $this->assertBlockType($phpBlock, Either::class . '<string, array<array-key, mixed>>');
    }
}
