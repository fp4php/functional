<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements NonEmptySeqChainableOps
 * @psalm-require-implements NonEmptySeqCollector
 */
trait NonEmptySeqChainable
{
    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collectUnsafe(IterableOnce::of(function () use ($callback) {
            foreach ($this as $elem) {
                /** @var TV $e */
                $e = $elem;

                yield $callback($e);
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return self::collectUnsafe(IterableOnce::of(function () use ($elem) {
            foreach ($this as $prefixElem) {
                yield $prefixElem;
            }

            yield $elem;
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collectUnsafe(IterableOnce::of(function() use ($suffix) {
            foreach ($this as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return self::collectUnsafe(IterableOnce::of(function () use ($elem) {
            yield $elem;

            foreach ($this as $prefixElem) {
                yield $prefixElem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collectUnsafe(IterableOnce::of(function() use ($prefix) {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this as $suffixElem) {
                yield $suffixElem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
     */
    public function tap(callable $callback): self
    {
        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;
            $callback($e);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-param callable(TV): (int|string) $callback
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        $pairs = $this->map(function($elem) use ($callback) {
            /** @var TV $elem */
            return [$callback($elem), $elem];
        });

        return self::collectUnsafe(HashMap::collectPairs($pairs)->values());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return self<TV>
     */
    public function sorted(callable $cmp): self
    {
        $sorted = $this->toArray();

        /**
         * @todo implement stable pure sort
         * @psalm-suppress ImpureFunctionCall, InvalidArgument
         */
        usort($sorted, $cmp);

        return self::collectUnsafe($sorted);
    }
}

