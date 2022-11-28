<?php

declare(strict_types=1);

namespace Tests\Static\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyArrayList;

final class ToMergedArrayTest
{
    /**
     * @param ArrayList<list<int>> $list
     * @return list<int>
     */
    public function asList(ArrayList $list): array
    {
        return $list->toMergedArray();
    }

    /**
     * @param ArrayList<non-empty-list<int>> $list
     * @return list<int>
     */
    public function asListFromNonEmptyList(ArrayList $list): array
    {
        return $list->toMergedArray();
    }

    /**
     * @param ArrayList<array<string, int>> $list
     * @return array<string, int>
     */
    public function asArray(ArrayList $list): array
    {
        return $list->toMergedArray();
    }

    /**
     * @param ArrayList<non-empty-array<string, int>> $list
     * @return array<string, int>
     */
    public function asArrayFromNonEmptyArray(ArrayList $list): array
    {
        return $list->toMergedArray();
    }

    /**
     * @param NonEmptyArrayList<non-empty-list<int>> $list
     * @return non-empty-list<int>
     */
    public function asNonEmptyList(NonEmptyArrayList $list): array
    {
        return $list->toNonEmptyMergedArray();
    }

    /**
     * @param NonEmptyArrayList<non-empty-array<string, int>> $list
     * @return non-empty-array<string, int>
     */
    public function asNonEmptyArray(NonEmptyArrayList $list): array
    {
        return $list->toNonEmptyMergedArray();
    }
}
