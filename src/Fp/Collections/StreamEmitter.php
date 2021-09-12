<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Unit;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamEmitter
{
    /**
     * Create singleton stream with one element
     *
     * REPL:
     * >>> Stream::emit(1)->toArray()
     * => [1]
     *
     * @template TVI
     * @param TVI $elem
     * @return Stream<TVI>
     */
    public static function emit(mixed $elem): Stream;

    /**
     * Emits elements from iterable source
     *
     * REPL:
     * >>> Stream::emits([1, 2])->toArray()
     * => [1, 2]
     *
     * @template TVI
     * @param iterable<TVI> $source
     * @return Stream<TVI>
     */
    public static function emits(iterable $source): Stream;

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeat()->toArray()
     * => [1, 1]
     *
     * @return Stream<TV>
     */
    public function repeat(): Stream;

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeatN(3)->toArray()
     * => [1, 1, 1]
     *
     * @return Stream<TV>
     */
    public function repeatN(int $times): Stream;

    /**
     * Discrete stream that emits elapsed duration since the start time of stream consumption.
     * For example: awakeEvery(5) will return (approximately) 5s, 10s, 15s, and will lie dormant between emitted values.
     *
     * @return Stream<int>
     */
    public static function awakeEvery(int $seconds): Stream;

    /**
     * Creates an infinite stream that always returns the supplied value
     *
     * REPL:
     * >>> Stream::constant(0)->take(3)->toArray()
     * => [0, 0, 0]
     *
     * @template TVI
     * @param TVI $const
     * @return Stream<TVI>
     */
    public static function constant(mixed $const): Stream;

    /**
     * Creates int stream of given range
     *
     * REPL:
     * >>> Stream::range(0, 10, 2)->toArray()
     * => [0, 2, 4, 6, 8]
     *
     * @param positive-int $start
     * @param positive-int $stopExclusive
     * @param positive-int $by
     * @return Stream<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): Stream;

    /**
     * Creates an infinite stream
     *
     * REPL:
     * >>> Stream::infinite()->map(fn() => rand(0, 1))->take(2)->toArray()
     * => [0, 1]
     *
     * @return Stream<Unit>
     */
    public static function infinite(): Stream;
}
