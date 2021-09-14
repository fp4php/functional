<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamCastableOps
{
    /**
     * @return list<TV>
     */
    public function toArray(): array;

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;

    /**
     * @param string $path file path
     * @param bool $append append to an existing file
     */
    public function toFile(string $path, bool $append = false): void;
}
