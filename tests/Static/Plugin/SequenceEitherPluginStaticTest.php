<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Functional\Either\Either;
use InvalidArgumentException;
use RuntimeException;

use function Fp\Collection\at;
use function Fp\Collection\sequenceEither;
use function Fp\Collection\sequenceEitherAcc;
use function Fp\Collection\sequenceEitherMerged;
use function Fp\Collection\sequenceEitherMergedT;
use function Fp\Collection\sequenceEitherT;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveNonEmptyString;
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

    /**
     * @param list<Either<string, int>> $list
     * @return Either<non-empty-list<string>, list<int>>
     */
    public function sequenceAccWithList(array $list): Either
    {
        return sequenceEitherAcc($list);
    }

    /**
     * @param non-empty-list<Either<string, int>> $list
     * @return Either<non-empty-list<string>, non-empty-list<int>>
     */
    public function sequenceAccWithNonEmptyList(array $list): Either
    {
        return sequenceEitherAcc($list);
    }

    /**
     * @param array<non-empty-string, Either<string, int>> $list
     * @return Either<non-empty-array<non-empty-string, string>, array<non-empty-string, int>>
     */
    public function sequenceAccWithArray(array $list): Either
    {
        return sequenceEitherAcc($list);
    }

    /**
     * @param non-empty-array<non-empty-string, Either<string, int>> $list
     * @return Either<non-empty-array<non-empty-string, string>, non-empty-array<non-empty-string, int>>
     */
    public function sequenceAccWithNonEmptyArray(array $list): Either
    {
        return sequenceEitherAcc($list);
    }

    /**
     * @param array<string, mixed> $data
     * @return Either<InvalidArgumentException, array{non-empty-string, int}>
     */
    public function sequenceEitherT(array $data): Either
    {
        return sequenceEitherT(
            at($data, 'name')
                ->flatMap(proveNonEmptyString(...))
                ->toRight(fn() => new InvalidArgumentException()),
            at($data, 'age')
                ->flatMap(proveInt(...))
                ->toRight(fn() => new InvalidArgumentException()),
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return Either<non-empty-list<-1|-2>, array{non-empty-string, int}>
     */
    public function sequenceEitherMergedT(array $data): Either
    {
        return sequenceEitherMergedT(
            at($data, 'name')->flatMap(proveNonEmptyString(...))->toRight(fn() => [-1]),
            at($data, 'age')->flatMap(proveInt(...))->toRight(fn() => [-2]),
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return Either<non-empty-list<-1|-2>, array{
     *     n: non-empty-string,
     *     a: int,
     * }>
     */
    public function sequenceEitherMerged(array $data): Either
    {
        return sequenceEitherMerged([
            'n' => at($data, 'name')->flatMap(proveNonEmptyString(...))->toRight(fn() => [-1]),
            'a' => at($data, 'age')->flatMap(proveInt(...))->toRight(fn() => [-2]),
        ]);
    }

    /**
     * @return Either<
     *     array{
     *         name?: "Is not non-empty-string",
     *         age?: "Is not int",
     *         address?: array{
     *             postcode?: "Is not int",
     *             city?: "Is not string"
     *         }
     *     },
     *     array{
     *         name: non-empty-string,
     *         age: int,
     *         address: array{
     *             postcode: int,
     *             city: non-empty-string
     *         }
     *     }
     * >
     */
    public function sequenceAccShape(array $data): Either
    {
        return sequenceEitherAcc([
            'name' => at($data, 'name')->flatMap(proveNonEmptyString(...))->toRight(fn() => 'Is not non-empty-string'),
            'age' => at($data, 'age')->flatMap(proveInt(...))->toRight(fn() => 'Is not int'),
            'address' => sequenceEitherAcc([
                'postcode' => at($data, 'postcode')->flatMap(proveInt(...))->toRight(fn() => 'Is not int'),
                'city' => at($data, 'city')->flatMap(proveNonEmptyString(...))->toRight(fn() => 'Is not string'),
            ]),
        ]);
    }
}
