<?php

declare(strict_types=1);

namespace Fp\Util;

use const PHP_EOL;

function writeln(string $text): void
{
    print_r($text . PHP_EOL);
}
