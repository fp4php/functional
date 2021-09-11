<?php

declare(strict_types=1);

namespace Fp\Collections;

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
     * @return self<TVI>
     */
    public static function emit(mixed $elem): self;

    /**
     * Emits elements from iterable source
     *
     * REPL:
     * >>> Stream::emits([1, 2])->toArray()
     * => [1, 2]
     *
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function emits(iterable $source): self;

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeat()->toArray()
     * => [1, 1]
     *
     * @return self<TV>
     */
    public function repeat(): self;

    /**
     * Repeat this stream
     *
     * REPL:
     * >>> Stream::emit(1)->repeatN(3)->toArray()
     * => [1, 1, 1]
     *
     * @return self<TV>
     */
    public function repeatN(int $times): self;

    /**
     * Discrete stream that every d emits elapsed duration since the start time of stream consumption.
     * For example: awakeEvery(5) will return (approximately) 5s, 10s, 15s, and will lie dormant between emitted values.
     *
     * @return self<int>
     */
    public static function awakeEvery(int $seconds): self;
}
