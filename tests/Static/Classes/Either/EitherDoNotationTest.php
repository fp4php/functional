<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Fp\Functional\Either\Either;
use Tests\PhpBlockTestCase;

final class EitherDoNotationTest extends PhpBlockTestCase
{
    public function testWithFilter(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;

                /** @psalm-trace $result */
                $result = Either::do(function() {
                    $num1 = yield Either::right(10);
                    $num2 = yield Either::right(20);

                    if ($num1 < 10) {
                        return yield Either::left("num1 less than 10");
                    }

                    return $num1 + $num2;
                });
            ',
            strtr(
                'Either<"num1 less than 10", positive-int>',
                [
                    'Either' => Either::class,
                ])
        );
    }
}
