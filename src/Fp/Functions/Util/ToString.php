<?php

declare(strict_types=1);

namespace Fp\Util;

use Fp\Operations\ToStringOperation;

function toString(mixed $value): string
{
    return ToStringOperation::of($value);
}
