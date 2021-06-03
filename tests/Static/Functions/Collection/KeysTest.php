<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class KeysTest extends PhpBlockTestCase
{
    public function testArrayToKeys(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** @psalm-return array<string, int> */
            function getArrayWithStringKeys(): array { return []; }
            /** @psalm-trace $stringKeys */
            $stringKeys = Fp\Collection\keys(getArrayWithStringKeys());


            /** @psalm-return non-empty-array<string, int> */
            function getNonEmptyArrayWithStringKeys(): array { return []; }
            /** @psalm-trace $nonEmptyStringKeys */
            $nonEmptyStringKeys = Fp\Collection\keys(getNonEmptyArrayWithStringKeys());


            /** @psalm-return array<int, int> */
            function getArrayWithIntKeys(): array { return []; }
            /** @psalm-trace $intKeys */
            $intKeys = Fp\Collection\keys(getArrayWithIntKeys());


            /** @psalm-return non-empty-array<int, int> */
            function getNonEmptyArrayWithIntKeys(): array { return []; }
            /** @psalm-trace $nonEmptyIntKeys */
            $nonEmptyIntKeys = Fp\Collection\keys(getNonEmptyArrayWithIntKeys());


            /** @psalm-return array<int|string, int> */
            function getArrayWithKeys(): array { return []; }
            /** @psalm-trace $keys */
            $keys = Fp\Collection\keys(getArrayWithKeys());


            /** @psalm-return non-empty-array<int|string, int> */
            function getNonEmptyArrayWithKeys(): array { return []; }
            /** @psalm-trace $nonEmptyKeys */
            $nonEmptyKeys = Fp\Collection\keys(getNonEmptyArrayWithKeys());
        ';

        $this->assertBlockTypes($phpBlock,
            'list<string>',
            'non-empty-list<string>',
            'list<int>',
            'non-empty-list<int>',
            'list<int|string>',
            'non-empty-list<int|string>',
        );
    }
}
