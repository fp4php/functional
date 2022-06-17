<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;
use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @extends AbstractOperation<TK, TV>
 */
class TraverseOptionOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV, TK): Option<TVO> $f
     * @return Option<Generator<TK, TVO>>
     */
    public function __invoke(callable $f): Option
    {
        $collection = [];

        foreach ($this->gen as $key => $value) {
            $mapped = $f($value, $key);

            if ($mapped->isNone()) {
                return Option::none();
            }

            $collection[] = [$key, $mapped->get()];
        }

        return Option::some(asGenerator(function() use ($collection) {
            foreach ($collection as [$key, $value]) {
                yield $key => $value;
            }
        }));
    }
}
