<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Option;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

final class OptionTest extends TestCase
{
    public function testMap(): void
    {
        $some = Some::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $none = Some::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(function(int $s) {
                /** @var int|null $n */
                $n = null;

                return $n;
            })
            ->map(fn(int $s) => $s + 1);

        $this->assertEquals(3, $some->get());
        $this->assertNull($none->get());
    }

    public function testFlatMap(): void
    {
        $some = Some::of(1)
            ->flatMap(fn(int $s) => Option::of($s + 1))
            ->flatMap(fn(int $s) => Option::of($s + 1));

        $none = Some::of(1)
            ->flatMap(fn(int $s) => Option::of($s + 1))
            ->flatMap(function(int $s) {
                /** @var int|null $n */
                $n = null;

                return Option::of($n);
            })
            ->flatMap(fn(int $s) => Option::of($s + 1));

        $this->assertEquals(3, $some->get());
        $this->assertNull($none->get());
    }
}
