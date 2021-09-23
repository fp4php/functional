<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements NonEmptySeq
 */
trait NonEmptySeqTerminable
{
    /**
     * Alias for {@see NonEmptySeq::at()}
     *
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        $first = null;

        foreach ($this as $idx => $element) {
            if ($idx === $index) {
                $first = $element;
                break;
            }
        }

        return Option::fromNullable($first);
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
     * @psalm-return TV
     */
    public function head(): mixed
    {
        $head = null;

        foreach ($this as $element) {
            $head = $element;
            break;
        }

        return Option::fromNullable($head)->getUnsafe();
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
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed
    {
        /** @psalm-var TV $acc */
        $acc = $this->head();

        foreach ($this->tail() as $elem) {
            /** @psalm-var TV $cur */
            $cur = $elem;
            $acc = $callback($acc, $cur);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        return $this->last(fn() => true)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return NonEmptyMap<TKO, NonEmptySeq<TV>>
     * @psalm-suppress ImpureMethodCall
     */
    public function groupBy(callable $callback): NonEmptyMap
    {
        $buffer = new HashMapBuffer();

        foreach ($this as $elem) {
            /** @var TV $e */
            $e = $elem;
            $key = $callback($e);

            /**
             * @psalm-var Option<NonEmptySeq<TV>> $optionalGroup
             */
            $optionalGroup = $buffer->get($key);

            $buffer->update($key, $optionalGroup->fold(
                fn(NonEmptySeq $group): NonEmptySeq => $group->prepended($e),
                fn(): NonEmptySeq => new NonEmptyLinkedList($e, Nil::getInstance())
            ));
        }

        return new NonEmptyHashMap($buffer->toHashMap());
    }
}

