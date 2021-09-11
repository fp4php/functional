<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements StreamEmitter
 */
trait StreamChainable
{
    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::emits(IterableOnce::of(function () use ($callback) {
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
        return self::emits(IterableOnce::of(function () use ($elem) {
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
        return self::emits(IterableOnce::of(function() use ($suffix) {
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
        return self::emits(IterableOnce::of(function () use ($elem) {
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
        return self::emits(IterableOnce::of(function() use ($prefix) {
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
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return self::emits(IterableOnce::of(function () use ($predicate) {
            foreach ($this as $element) {
                /** @var TV $e */
                $e = $element;

                if ($predicate($e)) {
                    yield $e;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::emits(IterableOnce::of(function () use ($callback) {
            foreach ($this as $element) {
                /** @var TV $e */
                $e = $element;
                $result = $callback($e);

                if ($result->isSome()) {
                    yield $result->get();
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return $this->filter(fn(mixed $v) => !is_null($v));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return self<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        /** @var self<TVO> */
        return $this->filter(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::emits(IterableOnce::of(function () use ($callback) {
            foreach ($this as $element) {
                /** @var TV $e */
                $e = $element;
                $result = $callback($e);

                foreach ($result as $item) {
                    yield $item;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        return self::emits(IterableOnce::of(function () {
            $isFirst = true;

            foreach ($this as $elem) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                yield $elem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function takeWhile(callable $predicate): self
    {
        return self::emits(IterableOnce::of(function () use ($predicate) {
            foreach ($this as $elem) {
                /** @var TV $e */
                $e = $elem;

                if (!$predicate($e)) {
                    break;
                }

                yield $e;
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function dropWhile(callable $predicate): self
    {
        return self::emits(IterableOnce::of(function () use ($predicate) {
            $toggle = true;

            foreach ($this as $elem) {
                /** @var TV $e */
                $e = $elem;

                if (!($toggle = $toggle && $predicate($e))) {
                    yield $e;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function take(int $length): self
    {
        return self::emits(IterableOnce::of(function () use ($length) {
            foreach ($this as $i => $elem) {
                if ($i === $length) {
                    break;
                }

                yield $elem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function drop(int $length): self
    {
        return self::emits(IterableOnce::of(function () use ($length) {
            foreach ($this as $i => $elem) {
                if ($i < $length) {
                    continue;
                }

                yield $elem;
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
        return self::emits(IterableOnce::of(function () use ($callback) {
            foreach ($this as $elem) {
                yield $elem;
                $callback($elem);
            }
        }));
    }
}

