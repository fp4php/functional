<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Fp\Functional\Either\Either;

use function Fp\Evidence\proveTrue;

final class EitherDoNotationStaticTest
{
    /**
     * @return Either<"num1 less than 10", int>
     */
    public function testWithFilter(): Either
    {
        return Either::do(function() {
            /** @var int $num1 */
            $num1 = yield Either::right(10);
            /** @var int $num2 */
            $num2 = yield Either::right(20);

            yield proveTrue($num1 < 10)->toRight(fn() => 'num1 less than 10');

            return $num1 + $num2;
        });
    }
}
