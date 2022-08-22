<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;
use function Fp\Evidence\proveOf;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class FilterOfOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Generator<TK, TVO>
     */
    public function __invoke(string|array $fqcn, bool $invariant = false): Generator
    {
        return asGenerator(function() use ($fqcn, $invariant) {
            foreach ($this->gen as $key => $value) {
                $option = proveOf($value, $fqcn, $invariant);

                if ($option->isSome()) {
                    yield $key => $option->get();
                }
            }
        });
    }
}
