<?php

declare(strict_types=1);

namespace Fp;

use RuntimeException;

function panic(string $message): never
{
    throw new RuntimeException($message);
}
