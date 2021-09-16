<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use PHPUnit\Framework\TestCase;

final class SeqCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals([1, 2, 3], ArrayList::collect([1, 2, 3])->toArray());
        $this->assertEquals([1, 2, 3], LinkedList::collect([1, 2, 3])->toArray());
    }
}
