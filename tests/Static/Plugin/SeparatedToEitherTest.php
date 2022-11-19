<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;

final class SeparatedToEitherTest
{
    /**
     * @param Separated<ArrayList<int>, ArrayList<string>> $separated
     * @return Either<ArrayList<int>, ArrayList<string>>
     */
    public function separatedArrayListToEither(Separated $separated): Either
    {
        return $separated->toEither();
    }

    /**
     * @param Separated<HashMap<int, string>, HashMap<string, int>> $separated
     * @return Either<HashMap<int, string>, HashMap<string, int>>
     */
    public function separatedHashMapToEither(Separated $separated): Either
    {
        return $separated->toEither();
    }
}
