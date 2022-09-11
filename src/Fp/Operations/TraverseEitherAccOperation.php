<?php

declare(strict_types=1);

namespace Fp\Operations;

use Closure;
use Fp\Collections\HashTable;
use Fp\Functional\Either\Either;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class TraverseEitherAccOperation extends AbstractOperation
{
    /**
     * @template E
     * @template TVO
     *
     * @param callable(TK, TV): Either<E, TVO> $f
     * @return Either<Generator<TK, E>, Generator<TK, TVO>>
     */
    public function __invoke(callable $f): Either
    {
        /** @psalm-var HashTable<TK, TVO> $rights */
        $rights = new HashTable();

        /** @psalm-var HashTable<TK, E> $lefts */
        $lefts = new HashTable();

        foreach ($this->gen as $key => $value) {
            $mapped = $f($key, $value);

            if ($mapped->isLeft()) {
                $lefts->update($key, $mapped->get());
            } else {
                $rights->update($key, $mapped->get());
            }
        }

        return !$lefts->isEmpty()
            ? Either::left($lefts->getKeyValueIterator())
            : Either::right($rights->getKeyValueIterator());
    }

    /**
     * @template E
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, Either<E, TVI> | Closure(): Either<E, TVI>> $collection
     * @return Either<Generator<TKI, E>, Generator<TKI, TVI>>
     */
    public static function id(iterable $collection): Either
    {
        return self::of($collection)(
            fn(mixed $_key, Either|Closure $i): Either => $i instanceof Closure ? $i() : $i
        );
    }
}
