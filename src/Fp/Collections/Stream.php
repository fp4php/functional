<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Iterator;
use IteratorIterator;
use LogicException;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamOps<TV>
 * @implements StreamCasts<TV>
 * @implements StreamEmitter<TV>
 * @implements Collection<TV>
 */
final class Stream implements StreamOps, StreamCasts, StreamEmitter, Collection
{
    /**
     * @use StreamChainable<TV>
     */
    use StreamChainable;

    /**
     * @use StreamUnchainable<TV>
     */
    use StreamUnchainable;

    /**
     * @use StreamConvertible<TV>
     */
    use StreamConvertible;

    /**
     * @psalm-readonly-allow-private-mutation $closed
     */
    private bool $closed = false;

    /**
     * @param iterable<TV> $emitter
     */
    private function __construct(private iterable $emitter)
    {

    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        $this->closed = !$this->closed
            ? true
            : throw new LogicException('Can not traverse closed stream');

        return is_array($this->emitter)
            ? new ArrayIterator($this->emitter)
            : new IteratorIterator($this->emitter);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $elem
     * @return self<TVI>
     */
    public static function emit(mixed $elem): self
    {
        return new self(IterableOnce::of(function () use ($elem) {
            yield $elem;
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function emits(iterable $source): self
    {
        return new self($source);
    }

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeat()
     * => Stream(1, 1)
     *
     * @return self<TV>
     */
    public function repeat(): self
    {
        return $this->repeatN(1);
    }

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeatN(3)
     * => Stream(1, 1, 1)
     *
     * @return self<TV>
     */
    public function repeatN(int $times): self
    {
        return new self(IterableOnce::of(function () use ($times) {
            /** @var Seq<TV> $buffer */
            $buffer = ArrayList::collect($this);

            foreach ($buffer as $elem) {
                yield $elem;
            }

            for($i = 0; $i < $times - 1; $i++) {
                foreach ($buffer as $elem) {
                    yield $elem;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $separator
     * @psalm-return Stream<TV|TVI>
     */
    public function intersperse(mixed $separator): Stream
    {
        return new self(IterableOnce::of(function () use ($separator) {
            $isFirst = true;

            foreach ($this as $elem) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    yield $separator;
                }

                yield $elem;
            }
        }));
    }
}
