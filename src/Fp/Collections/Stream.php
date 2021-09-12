<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Error;
use Generator;
use Iterator;
use IteratorIterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamOps<TV>
 * @implements StreamEmitter<TV>
 * @implements Collection<TV>
 */
final class Stream implements StreamOps, StreamEmitter, Collection
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
     * @use StreamCastable<TV>
     */
    use StreamCastable;

    /**
     * @psalm-readonly-allow-private-mutation $drained
     */
    private bool $drained = false;

    /**
     * @param iterable<TV> $emitter
     */
    private function __construct(private iterable $emitter) { }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        $this->drained = !$this->drained
            ? true
            : throw new Error('Can not traverse already drained stream');

        return is_array($this->emitter)
            ? new ArrayIterator($this->emitter)
            : new IteratorIterator($this->emitter);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $counter = 0;

        foreach ($this as $ignored) {
            $counter++;
        }

        return $counter;
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
     * @template TVI
     * @param TVI $const
     * @return self<TVI>
     */
    public static function constant(mixed $const): self
    {
        return new self(IterableOnce::of(function () use ($const) {
            while (true) {
                yield $const;
            }
        }));
    }

    /**
     * @inheritDoc
     * @param positive-int $start
     * @param positive-int $stopExclusive
     * @param positive-int $by
     * @return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self
    {
        $source = function () use ($start, $stopExclusive, $by): Generator {
            for ($i = $start; $i < $stopExclusive; $i += $by) {
                yield $i;
            }
        };

        return new self($source());
    }
}
