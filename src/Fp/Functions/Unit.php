<?php

declare(strict_types=1);

namespace Fp;

use Fp\Functional\Unit;

const unit = '\Fp\unit';

/**
 * Represents no return value for do-notation
 *
 * REPL:
 * >>> Option::do(function () use ($x) {
 *     yield proveTrue($x);
 *     return unit();
 * })
 * => Option<Unit>
 *
 */
function unit(): Unit
{
    return new Unit();
}
