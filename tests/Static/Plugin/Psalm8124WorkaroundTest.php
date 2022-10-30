<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Functional\Option\Option;

/**
 * @see https://github.com/vimeo/psalm/issues/8124
 */
final class Psalm8124WorkaroundTest
{
    /**
     * @param ArrayList<string> $list
     * @param HashSet<string> $set
     * @return array{
     *     ArrayList<string>,
     *     ArrayList<string>,
     *     ArrayList<string>,
     *     ArrayList<string>,
     *     ArrayList<array{int, string}>,
     *     ArrayList<array{int, string}>,
     *     ArrayList<int|string>,
     *     ArrayList<int|string>,
     *     ArrayList<int|string>,
     *     ArrayList<int|string>,
     *     LinkedList<string>,
     *     LinkedList<string>,
     *     LinkedList<string>,
     *     LinkedList<string>,
     *     LinkedList<array{int, string}>,
     *     LinkedList<array{int, string}>,
     *     LinkedList<int|string>,
     *     LinkedList<int|string>,
     *     LinkedList<int|string>,
     *     LinkedList<int|string>,
     * }
     */
    public function seqTest(ArrayList $list, HashSet $set): array
    {
        return [
            ArrayList::collect($list),
            ArrayList::collect([])->flatMap(fn() => $list),
            ArrayList::collect($set),
            ArrayList::collect([])->flatMap(fn() => $set),
            ArrayList::collect([1, 2, 3])->zip($list),
            ArrayList::collect([1, 2, 3])->zip($set),
            ArrayList::collect([1, 2, 3])->prependedAll($list),
            ArrayList::collect([1, 2, 3])->prependedAll($set),
            ArrayList::collect([1, 2, 3])->appendedAll($list),
            ArrayList::collect([1, 2, 3])->appendedAll($set),
            LinkedList::collect($list),
            LinkedList::collect([])->flatMap(fn() => $list),
            LinkedList::collect($set),
            LinkedList::collect([])->flatMap(fn() => $set),
            LinkedList::collect([1, 2, 3])->zip($list),
            LinkedList::collect([1, 2, 3])->zip($set),
            LinkedList::collect([1, 2, 3])->prependedAll($list),
            LinkedList::collect([1, 2, 3])->prependedAll($set),
            LinkedList::collect([1, 2, 3])->appendedAll($list),
            LinkedList::collect([1, 2, 3])->appendedAll($set),
        ];
    }

    /**
     * @param NonEmptyArrayList<int> $neList
     * @param NonEmptyArrayList<string> $otherNeList
     * @param ArrayList<string> $list
     * @param HashSet<string> $set
     * @return array{
     *     Option<NonEmptyArrayList<string>>,
     *     Option<NonEmptyArrayList<string>>,
     *     NonEmptyArrayList<array{int, string}>,
     *     NonEmptyArrayList<int|string>,
     *     NonEmptyArrayList<int|string>,
     *     NonEmptyArrayList<int|string>,
     *     NonEmptyArrayList<int|string>,
     *     NonEmptyArrayList<int|string>
     * }
     */
    public function nonEmptySeqTest(NonEmptyArrayList $neList, NonEmptyArrayList $otherNeList, ArrayList $list, HashSet $set): array
    {
        return [
            NonEmptyArrayList::collect($list),
            NonEmptyArrayList::collect($set),
            $neList->zip($otherNeList),
            $neList->prependedAll($list),
            $neList->prependedAll($set),
            $neList->appendedAll($list),
            $neList->appendedAll($set),
            $neList->appendedAll($otherNeList),
        ];
    }
}
