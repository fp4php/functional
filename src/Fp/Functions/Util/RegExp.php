<?php

declare(strict_types=1);

namespace Fp\Util;

use Fp\Functional\Option\Option;

use function Fp\Collection\at;
use function Fp\Evidence\proveNonEmptyString;

/**
 * Regular expression search
 * Returns None if not matched
 *
 * ```php
 * >>> regExpMatch('/[a-z]+(c)/', 'abc', 1);
 * => Some('c')
 * ```
 *
 * @param int|string $capturingGroup
 * @return Option<non-empty-string>
 */
function regExpMatch(string $expr, string $text, string|int $capturingGroup = 0): Option
{
    return Option::do(function () use ($expr, $text, $capturingGroup) {
        preg_match($expr . 'u', $text, $matches);
        $subMatch = yield at($matches, $capturingGroup);
        return yield proveNonEmptyString($subMatch);
    });
}