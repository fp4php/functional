<?php

declare(strict_types=1);

namespace Fp\Functional\Tuple;

/**
 * @psalm-immutable
 * @template T1
 * @template T2
 */
final class Tuple2
{
    /**
     * @param T1 $first
     * @param T2 $second
     */
    public function __construct(
        private mixed $first,
        private mixed $second,
    ) {}

    /**
     * @psalm-return T1
     */
    public function getFirst(): mixed
    {
        return $this->first;
    }

    /**
     * @psalm-return T2
     */
    public function getSecond(): mixed
    {
        return $this->second;
    }

    /**
     * @psalm-template TI1
     * @psalm-template TI2
     * @psalm-param array{TI1,TI2} $tuple
     * @psalm-return self<TI1,TI2>
     */
    public static function ofArray(array $tuple): self
    {
        return new self(
            $tuple[0],
            $tuple[1],
        );
    }
}
