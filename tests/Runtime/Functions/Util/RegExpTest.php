<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Util;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Util\regExpMatch;

final class RegExpTest extends TestCase
{
    /**
     * @param Option<non-empty-string> $expected
     * @dataProvider provideRegExpMatchCases
     */
    public function testRegExpMatch(string $expr, string $text, Option $expected, int|string $capturingGroup = 0): void
    {
        $this->assertEquals($expected, regExpMatch($expr, $text, $capturingGroup));
    }

    public function provideRegExpMatchCases(): iterable
    {
        yield 'No match' => [
            '/[0-9]/', 'a', Option::none(),
        ];
        yield 'Match' => [
            '/[0-9]/', '1', Option::some('1'),
        ];
        yield 'Capturing group' => [
            '/([0-9])([a-z])/', '1a', Option::some('a'), 2,
        ];
        yield 'Named capturing group' => [
            '/(?<num>[0-9])(?<str>[a-z])/', '1a', Option::some('a'), 'str',
        ];
    }
}
