<?php

declare(strict_types=1);

namespace Fp\Functional\Tuple;

/**
 * @psalm-immutable
 * @psalm-template T1
 */
final class Tuple1
{
    /**
     * @param T1 $first
     */
    public function __construct(
        private mixed $first,
    ) {}

    /**
     * @psalm-return T1
     */
    public function getFirst(): mixed
    {
        return $this->first;
    }

    /**
     * @psalm-template TI1
     * @psalm-param array{TI1} $tuple
     * @psalm-return self<TI1>
     */
    public static function ofArray(array $tuple): self
    {
        return new self(
            $tuple[0],
        );
    }

    /**
     * @return array{T1}
     */
    public function toArray(): array
    {
        return [
            $this->getFirst(),
        ];
    }
}
