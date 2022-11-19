<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes;

use Fp\Collections\HashContractGlobal;
use Fp\Collections\HashMap;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;

final class HashContractGlobalTest extends TestCase
{
    protected function setUp(): void
    {
        HashContractGlobal::add(
            class: Bar::class,
            equals: fn(Bar $lhs, mixed $rhs) => $rhs instanceof Bar && $lhs->a === $rhs->a,
            hashCode: fn(Bar $i) => md5(match (true) {
                is_bool($i->a) => $i->a ? 'true' : 'false',
                is_int($i->a) => (string) $i->a,
            }),
        );
    }

    protected function tearDown(): void
    {
        HashContractGlobal::remove(Bar::class);
    }

    public function testGlobalHashContract(): void
    {
        $hm = HashMap::collectPairs([
            [new Bar(1), 1],
            [new Bar(true), 2],
            [new Bar(false), 3],
        ]);

        $this->assertArrayHasKey(
            Bar::class,
            HashContractGlobal::getAllHashContracts(),
        );

        $this->assertInstanceOf(
            Some::class,
            $hm->get(new Bar(1)),
        );

        $this->assertInstanceOf(
            Some::class,
            $hm->get(new Bar(true)),
        );

        $this->assertInstanceOf(
            Some::class,
            $hm->get(new Bar(false)),
        );

        $this->assertInstanceOf(
            None::class,
            $hm->get(new Bar(2)),
        );
    }
}
