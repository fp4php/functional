<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Psalm\FunctionalPlugin;

/**
 * @see https://github.com/vimeo/psalm/issues/8124
 * When issue will be solved: remove stubs at {@see FunctionalPlugin::registerStub()}
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
    public function arrayListCollectionArrayList(ArrayList $list, HashSet $set): array
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
}
