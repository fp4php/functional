<?php

declare(strict_types=1);

namespace Tests\Static\Functions;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;

use function Fp\panic;

final class PanicStaticTest
{
    /**
     * @param Option<int> $maybeInt
     */
    public function withOption(Option $maybeInt): int
    {
        return $maybeInt->getOrCall(fn() => panic('is not int'));
    }

    /**
     * @param Either<string, int> $maybeInt
     */
    public function withEither(Either $maybeInt): int
    {
        return $maybeInt->getOrCall(fn() => panic('is not int'));
    }
}
