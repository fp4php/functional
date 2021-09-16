<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Functional\Unit;
use Generator;
use IteratorAggregate;

use function Fp\Callable\asGenerator;

/**
 * Note: stream iteration via foreach is terminal operation
 *
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamOps<TV>
 * @implements StreamEmitter<TV>
 * @implements IteratorAggregate<TV>
 */
final class Stream implements StreamOps, StreamEmitter, IteratorAggregate
{
    /**
     * @use StreamChainable<TV>
     */
    use StreamChainable;

    /**
     * @use StreamTerminable<TV>
     */
    use StreamTerminable;

    /**
     * @use StreamCastable<TV>
     */
    use StreamCastable;

    /**
     * @var Generator<TV>
     */
    private Generator $emitter;

    /**
     * @param iterable<TV> $emitter
     */
    private function __construct(iterable $emitter)
    {
        $this->emitter = asGenerator(fn() => $emitter);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $elem
     * @return self<TVI>
     */
    public static function emit(mixed $elem): self
    {
        return new self(asGenerator(function () use ($elem) {
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
        return new self(asGenerator(function () use ($seconds) {
            $elapsed = 0;
            $prevTime = time();

            while (true) {
                sleep($seconds);

                $curTime = time();
                $elapsed += $curTime - $prevTime;
                $prevTime = $curTime;

                yield $elapsed;
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $const
     * @return self<TVI>
     */
    public static function constant(mixed $const): self
    {
        return new self(asGenerator(function () use ($const) {
            while (true) {
                yield $const;
            }
        }));
    }

    /**
     * @inheritDoc
     * @return Stream<Unit>
     */
    public static function infinite(): Stream
    {
        return self::constant(Unit::getInstance());
    }

    /**
     * @inheritDoc
     * @param 0|positive-int $start
     * @param 0|positive-int $stopExclusive
     * @param positive-int $by
     * @return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self
    {
        return new self(asGenerator(function () use ($start, $stopExclusive, $by) {
            for ($i = $start; $i < $stopExclusive; $i += $by) {
                yield $i;
            }
        }));
    }
}
