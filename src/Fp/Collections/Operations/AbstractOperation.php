<?php

declare(strict_types=1);

namespace Fp\Collections\Operations;

use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class AbstractOperation
{
    /**
     * @var Generator<TK, TV>
     */
    protected Generator $input;

    /**
     *
     * @param iterable<TK, TV> $input
     */
    final public function __construct(iterable $input)
    {
        $this->input = asGenerator(fn() => $input);
    }

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $input
     * @return static<TKI, TVI>
     */
    public static function of(iterable $input): static
    {
        return new static($input);
    }

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $input
     * @return static<TKI, TVI>
     */
    public static function ofPairs(iterable $input): static
    {
        return new static(asGenerator(function () use ($input) {
            foreach ($input as $pair) {
                yield $pair[0] => $pair[1];
            }
        }));
    }
}
