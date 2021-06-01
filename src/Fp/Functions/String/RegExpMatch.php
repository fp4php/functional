<?php

declare(strict_types=1);

namespace Fp\Json;

use Fp\Functional\Option\Option;

use function Fp\Collection\at;
use function Fp\Evidence\proveNonEmptyString;
use function Symfony\Component\String\u;

/**
 * Regular expression search
 * Returns None if not matched
 *
 * REPL:
 * >>> regExpMatch('/[a-z]+(c)/', 'abc', 1);
 * => Some('c')
 *
 * @psalm-param positive-int|0 $capturingGroup
 * @psalm-return Option<string>
 */
function regExpMatch(string $expr, string $text, int $capturingGroup = 0): Option
{
    return Option::do(function () use ($expr, $text, $capturingGroup) {
        /** @var list<string> $matches */
        $matches = u($text)->match($expr);
        $subMatch = yield at($matches, $capturingGroup);
        return yield proveNonEmptyString($subMatch);
    });
}
