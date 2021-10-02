<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Entry;

use Fp\Collections\Entry;
use PHPUnit\Framework\TestCase;

final class EntryTest extends TestCase
{
    public function testCasts(): void
    {
        $this->assertEquals([1, 2], (new Entry(1, 2))->toArray());
    }
}
