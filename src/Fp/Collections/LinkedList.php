<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements LinearSeq<TV>
 */
class LinkedList implements LinearSeq
{
    /**
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return LinkedList<TVI>
     */
    public static function collect(iterable $source): LinkedList
    {
        $list = Nil::create();

        foreach ($source as $element) {
            $list = new Cons($element, $list);
        }

        return $list;
    }

    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }
}
