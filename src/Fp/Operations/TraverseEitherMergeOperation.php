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
final class TraverseEitherMergeOperation extends AbstractOperation
{
    /**
     * @template E
     * @template TVO
     *
     * @param callable(TK, TV): Either<non-empty-list<E>, TVO> $f
     * @return Either<non-empty-list<E>, Generator<TK, TVO>>
     */
    public function __invoke(callable $f): Either
    {
        /** @psalm-var HashTable<TK, TVO> */
        $rights = new HashTable();

        $lefts = [];

        foreach ($this->gen as $key => $value) {
            $mapped = $f($key, $value);

            if ($mapped->isRight()) {
                $rights->update($key, $mapped->get());
                continue;
            }

            foreach ($mapped->get() as $error) {
                $lefts[] = $error;
            }
        }

        return !empty($lefts) ? Either::left($lefts) : Either::right($rights->getKeyValueIterator());
    }

    /**
     * @template E
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, Either<non-empty-list<E>, TVI> | Closure(): Either<non-empty-list<E>, TVI>> $collection
     * @return Either<non-empty-list<E>, Generator<TKI, TVI>>
     */
    public static function id(iterable $collection): Either
    {
        return self::of($collection)(
            fn(mixed $_key, Either|Closure $i): Either => $i instanceof Closure ? $i() : $i
        );
    }
}
