<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveNull;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveUnion;
use function Fp\Evidence\union;
use function Fp\Evidence\unionT;

final class ProveUnionTest extends TestCase
{
    public function testProveUnion(): void
    {
        $evidences = [
            proveInt(...),
            proveString(...),
            proveNull(...),
        ];

        $this->assertEquals(Option::none(), proveUnion(42.00, $evidences));
        $this->assertEquals(Option::none(), proveUnion([42], $evidences));
        $this->assertEquals(Option::some(42), proveUnion(42, $evidences));
        $this->assertEquals(Option::some('str'), proveUnion('str', $evidences));
        $this->assertEquals(Option::some(null), proveUnion(null, $evidences));
    }

    public function testUnion(): void
    {
        $evidences = [
            proveInt(...),
            proveString(...),
            proveNull(...),
        ];

        $this->assertEquals(Option::none(), union($evidences)(42.00));
        $this->assertEquals(Option::none(), union($evidences)([42]));
        $this->assertEquals(Option::some(42), union($evidences)(42));
        $this->assertEquals(Option::some('str'), union($evidences)('str'));
        $this->assertEquals(Option::some(null), union($evidences)(null));
    }

    public function testUnionT(): void
    {
        $evidences = [
            proveInt(...),
            proveString(...),
            proveNull(...),
        ];

        $this->assertEquals(Option::none(), unionT(...$evidences)(42.00));
        $this->assertEquals(Option::none(), unionT(...$evidences)([42]));
        $this->assertEquals(Option::some(42), unionT(...$evidences)(42));
        $this->assertEquals(Option::some('str'), unionT(...$evidences)('str'));
        $this->assertEquals(Option::some(null), unionT(...$evidences)(null));
    }
}
