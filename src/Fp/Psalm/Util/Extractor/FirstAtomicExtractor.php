<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\head;

/**
 * @internal
 */
trait FirstAtomicExtractor
{
    /**
     * @psalm-return Option<TClosure|TCallable>
     */
    public static function getUnionFirstCallableAtomic(Union $union): Option
    {
        return Option::do(function () use ($union) {
            return yield head(asList(
                $union->getClosureTypes(),
                $union->getCallableTypes()
            ));
        });
    }
}
