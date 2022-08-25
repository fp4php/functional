<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Functional\Either\Either;
use InvalidArgumentException;
use RuntimeException;

use function Fp\Collection\sequenceEither;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveString;

final class SequenceEitherPluginStaticTest
{
    /**
     * @param array<string, Either<RuntimeException, int>> $values
     * @return Either<RuntimeException, array<string, int>>
     */
    public function sequenceArray(array $values): Either
    {
        return sequenceEither($values);
    }

    /**
     * @param non-empty-array<string, Either<RuntimeException, int>> $values
     * @return Either<RuntimeException, non-empty-array<string, int>>
     */
    public function sequenceNonEmptyArray(array $values): Either
    {
        return sequenceEither($values);
    }

    /**
     * @param list<Either<RuntimeException, int>> $values
     * @return Either<RuntimeException, list<int>>
     */
    public function sequenceList(array $values): Either
    {
        return sequenceEither($values);
    }

    /**
     * @param non-empty-list<Either<RuntimeException, int>> $values
     * @return Either<RuntimeException, non-empty-list<int>>
     */
    public function sequenceNonEmptyList(array $values): Either
    {
        return sequenceEither($values);
    }

    /**
     * @return Either<InvalidArgumentException|RuntimeException, array{name: string, age: int}>
     */
    public function sequenceShape(mixed $name, mixed $age): Either
    {
        return sequenceEither([
            'name' => proveString($name)->toRight(fn() => new InvalidArgumentException('Invalid name')),
            'age' => proveInt($age)->toRight(fn() => new RuntimeException('Invalid age')),
        ]);
    }

    /**
     * @return Either<InvalidArgumentException|RuntimeException, array{string, int}>
     */
    public function sequenceTuple(mixed $name, mixed $age): Either
    {
        return sequenceEither([
            proveString($name)->toRight(fn() => new InvalidArgumentException('Invalid name')),
            proveInt($age)->toRight(fn() => new RuntimeException('Invalid age')),
        ]);
    }

    /**
     * @return Either<InvalidArgumentException|RuntimeException, array{name: string, age: int}>
     */
    public function sequenceLazyShape(mixed $name, mixed $age): Either
    {
        return sequenceEither([
            'name' => fn() => proveString($name)->toRight(fn() => new InvalidArgumentException('Invalid name')),
            'age' => fn() => proveInt($age)->toRight(fn() => new RuntimeException('Invalid age')),
        ]);
    }

    /**
     * @return Either<InvalidArgumentException|RuntimeException, array{string, int}>
     */
    public function sequenceLazyTuple(mixed $name, mixed $age): Either
    {
        return sequenceEither([
            fn() => proveString($name)->toRight(fn() => new InvalidArgumentException('Invalid name')),
            fn() => proveInt($age)->toRight(fn() => new RuntimeException('Invalid age')),
        ]);
    }
}
