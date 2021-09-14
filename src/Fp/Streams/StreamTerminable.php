<?php

declare(strict_types=1);

namespace Fp\Streams;

use ArrayIterator;
use Error;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;
use IteratorIterator;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements StreamTerminalOps
 */
trait StreamTerminable
{
    /**
     * @psalm-readonly-allow-private-mutation $drained
     */
    private bool $drained = false;

    /**
     * Note: You can not iterate the stream the second time
     *
     * @inheritDoc
     * @return Generator<TV>
     */
    public function getIterator(): Generator
    {
        $this->drained = !$this->drained
            ? true
            : throw new Error('Can not drain already drained stream');

        return $this->emitter;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;

            if (!$predicate($e)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->every(fn(mixed $v) => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        $isExists = false;

        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;

            if ($predicate($e)) {
                $isExists = true;
                break;
            }
        }
        return $isExists;
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->firstOf($fqcn, $invariant)->fold(
            fn() => true,
            fn() => false,
        );
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        $first = null;

        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;

            if ($predicate($e)) {
                $first = $e;
                break;
            }
        }

        return Option::fromNullable($first);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        /** @var Option<TVO> */
        return $this->first(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        $acc = $init;

        foreach ($this as $elem) {
            /** @var TV $cur */
            $cur = $elem;
            $acc = $callback($acc, $cur);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        $acc = null;
        $isHead = true;

        foreach ($this as $elem) {
            if ($isHead) {
                $isHead = false;
                $acc = $elem;
                continue;
            }

            /** @psalm-var TV|TA $a */
            $a = $acc;

            /** @psalm-var TV $cur */
            $cur = $elem;

            $acc = $callback($a, $cur);
        }

        return Option::fromNullable($acc);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        $head = null;

        foreach ($this as $element) {
            $head = $element;
            break;
        }

        return Option::fromNullable($head);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        $last = null;

        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;

            if ($predicate($e)) {
                $last = $e;
            }
        }

        return Option::fromNullable($last);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option
    {
        return $this->last(fn() => true);
    }

    /**
     * @inheritDoc
     */
    public function drain(): void
    {
        foreach ($this as $ignored) { }
    }
}

