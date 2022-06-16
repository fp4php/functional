<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;
use function Fp\Evidence\proveOf;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class FilterOfOperation extends AbstractOperation
{
    /**
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn
     * @psalm-return Generator<TK, TVO>
     */
    public function __invoke(string $fqcn, bool $invariant = false): Generator
    {
        return asGenerator(function () use ($fqcn, $invariant) {
            foreach ($this->gen as $key => $value) {
                $option = proveOf($value, $fqcn, $invariant);

                if ($option->isSome()) {
                    yield $key => $option->get();
                }
            }
        });
    }
}
