<?php

declare(strict_types=1);

namespace Fp;

use Fp\Functional\Unit;

const unit = '\Fp\unit';

/**
 * Represents no return value for do-notation
 *
 * $option = Option::do(function () use ($x) {
 *     yield proveTrue($x);
 *     return unit();
 * })
 *
 * $option type is Option<Unit>
 */
function unit(): Unit
{
    return new Unit();
}
