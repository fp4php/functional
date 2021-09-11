<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Error;
use Generator;
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
     * @todo
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
            : throw new Error('Can not traverse closed stream');

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
     * @inheritDoc
     * @return self<int>
     */
    public static function awakeEvery(int $seconds): self
    {
        $source = function () use ($seconds): Generator {
            $elapsed = 0;
            $prevTime = time();

            while (true) {
                sleep($seconds);

                $curTime = time();
                $elapsed += $curTime - $prevTime;
                $prevTime = $curTime;

                yield $elapsed;
            }
        };

        return new self($source());
    }

    /**
     * @inheritDoc
     * @return self<TV>
     */
    public function repeat(): self
    {
        return $this->repeatN(1);
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     * @psalm-return Stream<TV>
     */
    public function lines(): Stream
    {
        return $this->tap(function ($elem) {
            echo ((string) $elem) . PHP_EOL;
        });
    }


    /**
     * @inheritDoc
     */
    public function drain(): void
    {
        foreach ($this as $ignored) { }
    }
}
