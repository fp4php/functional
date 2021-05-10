<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\PartialFunctionReturnTypeProvider;

/**
 * @see partialLeft alias
 * @see PartialFunctionReturnTypeProvider
 */
function partial(callable $callback, mixed ...$args): callable
{
    return partialLeft($callback, $args);
}
