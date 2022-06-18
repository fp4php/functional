<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;
use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class FilterMapOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV, TK): Option<TVO> $f
     * @return Generator<TK, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $res = $f($value, $key);

                if ($res->isSome()) {
                    yield $key => $res->get();
                }
            }
        });
    }
}
