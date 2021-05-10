<?php

declare(strict_types=1);

namespace Fp\Functional\Tuple;

/**
 * @psalm-immutable
 * @template T1
 * @template T2
 * @template T3
 */
final class Tuple3
{
    /**
     * @param T1 $first
     * @param T2 $second
     * @param T3 $third
     */
    public function __construct(
        private mixed $first,
        private mixed $second,
        private mixed $third,
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
     * @psalm-return T3
     */
    public function getThird(): mixed
    {
        return $this->third;
    }

    /**
     * @psalm-template TI1
     * @psalm-template TI2
     * @psalm-template TI3
     * @psalm-param array{TI1,TI2,TI3} $tuple
     * @psalm-return self<TI1,TI2,TI3>
     */
    public static function ofArray(array $tuple): self
    {
        return new self(
            $tuple[0],
            $tuple[1],
            $tuple[2],
        );
    }
}
