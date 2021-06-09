<?php

declare(strict_types=1);

namespace Fp\Json;

use function Fp\Collection\last;
use function Fp\Collection\map;

/**
 * Explode string by one or multiple separators
 * and return last element from exploded elements
 *
 * REPL:
 * >>> lastExploded('Fp\Functional\Option\Option', '\\');
 * => 'Option'
 * >>> lastExploded('Option', '\\');
 * => 'Option'
 * >>> lastExploded('Fp\Functional\Option/Option', ['\\', '/']);
 * => 'Option'
 *
 * @psalm-param string|non-empty-list<string> $separator
 */
function lastExploded(string $subject, string|array $separator): string
{
    $separators = is_array($separator) ? $separator : [$separator];

    $quotedSeparators = map(
        $separators,
        fn(string $s) => sprintf(
            '(%s)',
            preg_quote($s, '/')
        )
    );

    $implodedSeparators = implode('|', $quotedSeparators);

    $exploded = preg_split(
        sprintf('/%s/', $implodedSeparators),
        $subject
    );

    $exploded = $exploded !== false ? $exploded : [$subject];

    return last($exploded)->getOrElse($subject);
}
