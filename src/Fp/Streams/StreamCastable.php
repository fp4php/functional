<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use SplFileObject;

use function Fp\Callable\asGenerator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements StreamCastableOps
 */
trait StreamCastable
{
    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this);
    }

    /**
     * @inheritDoc
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this);
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap
    {
        return HashMap::collectPairs(asGenerator(function () use ($callback) {
            foreach ($this as $elem) {
                /** @var TV $e */
                $e = $elem;
                yield $callback($e);
            }
        }));
    }

    /**
     * @inheritDoc
     */
    public function toFile(string $path, bool $append = false): void
    {
        $file = new SplFileObject($path, $append ? 'a' : 'w');

        foreach ($this as $elem) {
            /** @psalm-suppress ImpureMethodCall */
            $file->fwrite((string) $elem);
        }

        $file = null;
    }
}

