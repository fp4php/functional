<?php

declare(strict_types=1);

namespace Fp;

use Fp\Functional\Unit;

const unit = '\Fp\unit';

/**
 * Represents no return value for do-notation
 *
 * ```php
 * >>> Option::do(function () {
 *     yield proveTrue(true);
 *     return unit();
 * })
 * => Some(Unit())
 * ```
 */
function unit(): Unit
{
    return Unit::getInstance();
}
