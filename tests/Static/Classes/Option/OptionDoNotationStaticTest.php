<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;
use Fp\Functional\Unit;

use function Fp\unit;

final class OptionDoNotationStaticTest
{
    /**
     * @return Option<Unit>
     */
    public function testUnitReturn(): Option
    {
        return Option::do(function () {
            yield Option::fromNullable(false);
            return unit();
        });
    }

    /**
     * @return Option<1|Unit>
     */
    public function testUnitReturnConditionally(): Option
    {
        return Option::do(function () {
            yield Option::fromNullable(false);

            if (rand(0, 1) === 1) {
                return 1;
            }

            return unit();
        });
    }

    /**
     * @return Option<positive-int>
     */
    public function testWithFilter(): Option
    {
        return Option::do(function() {
            /** @var int $num */
            $num = yield Option::some(10);

            if ($num < 10) {
                return yield Option::none();
            }

            return $num + 32;
        });
    }
}
