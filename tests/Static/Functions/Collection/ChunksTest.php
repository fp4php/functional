<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class ChunksTest extends PhpBlockTestCase
{
    public function testChunksWithArray(): void
    {
        $this->assertBlockTypes(/** @lang InjectablePHP */
            '
            use function Fp\Collection\chunks;

            /** 
             * @psalm-return array<int, string> 
             */
            function getCollection(): array { return []; }

            $result = chunks(getCollection(), 2);
        ', 'Generator<mixed,list<string>,mixed,mixed>');
    }

}
