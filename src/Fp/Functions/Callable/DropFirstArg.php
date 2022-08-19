<?php

declare(strict_types=1);

namespace Fp\Callable;

use Closure;

/**
 * @template A
 * @template B
 *
 * @param callable(A): B $for
 * @return Closure(mixed, A): B
 */
function dropFirstArg(callable $for): Closure
{
    return fn(mixed $_, mixed $v) => $for($v);
}
