<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Util;

use RuntimeException;
use Tests\Mock\Bar;
use Tests\Mock\Baz;
use PHPUnit\Framework\TestCase;

use function Fp\Util\toString;

final class ToStringTest extends TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testToString(mixed $value, string $expected): void
    {
        $this->assertEquals($expected, toString($value));
    }

    /**
     * @return list<array{mixed, string}>
     */
    public function provideCases(): array
    {
        return [
            [42, '42'],
            [42.00, '42.00'],
            [42.42, '42.42'],
            [true, 'true'],
            [false, 'false'],
            ['string', '"string"'],
            ['type: "string"', '"type: \"string\""'],
            [new Baz, 'Baz()'],
            [new Bar(a: 42), 'Tests\Mock\Bar'],
            [new RuntimeException(), 'RuntimeException()'],
            [new RuntimeException('Error occurred'), 'RuntimeException("Error occurred")'],
            [[1, 2, 3], '[1, 2, 3]'],
            [['fst' => 1, 'snd' => 2, 'thr' => 3], '["fst" => 1, "snd" => 2, "thr" => 3]'],
        ];
    }
}
