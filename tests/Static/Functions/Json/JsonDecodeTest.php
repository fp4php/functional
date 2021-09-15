<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Json;

use Fp\Functional\Either\Either;

use function Fp\Json\jsonDecode;

final class JsonDecodeTest
{
    /**
     * @return Either<string, array|scalar>
     */
    public function testDecode(): Either
    {
        return jsonDecode("{}");
    }
}
