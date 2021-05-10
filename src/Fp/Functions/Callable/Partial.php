<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\PartialPlugin;

/**
 * @see partialLeft alias
 * @see PartialPlugin
 */
function partial(callable $callback, mixed ...$args): callable
{
    return partialLeft($callback, $args);
}
