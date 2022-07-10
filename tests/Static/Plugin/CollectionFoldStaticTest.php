<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class CollectionFoldStaticTest
{
    public function calculatingTheSumOfAllNumbers(): int
    {
        return ArrayList::collect([1, 2, 3])
            ->fold(0)(fn($sum, $num) => $sum + $num);
    }

    public function concatAllStrings(): string
    {
        return ArrayList::collect(['fst', 'snd', 'thr'])
            ->fold('')(fn($concatenated, $string) => $concatenated . $string);
    }

    public function countingElements(): int
    {
        return ArrayList::collect([1, 2, 3])
            ->fold(0)(fn($count) => $count + 1);
    }

    public function findingMaxValue(): int
    {
        return ArrayList::collect([1, 2, 3])
            ->fold(PHP_INT_MIN)(fn($min, $num) => max($min, $num));
    }

    public function findingMinValue(): int
    {
        return ArrayList::collect([1, 2, 3])
            ->fold(PHP_INT_MAX)(fn($min, $num) => min($min, $num));
    }

    /**
     * @return ArrayList<string>
     */
    public function mapEachValueToString(): ArrayList
    {
        return ArrayList::collect([1, 2, 3])
            ->fold(ArrayList::empty())(fn($list, $num) => $list->appended((string) $num));
    }

    /**
     * @return ArrayList<int>
     */
    public function mergeAllChunksIntoOneArrayList(): ArrayList
    {
        return ArrayList::collect([[1, 2, 3], [4, 5, 6], [7, 8, 9]])
            ->fold(ArrayList::empty())(fn($merged, $chunk) => $merged->appendedAll($chunk));
    }

    /**
     * @return list<int>
     */
    public function mergeAllChunksIntoOneList(): array
    {
        return ArrayList::collect([[1, 2, 3], [4, 5, 6], [7, 8, 9]])
            ->fold([])(fn($merged, $chunk) => [...$merged, ...$chunk]);
    }

    /**
     * @return list<int>
     */
    public function mergeAllChunksIntoOneNativeArray(): array
    {
        return ArrayList::collect([[1, 2, 3], [4, 5, 6], [7, 8, 9]])
            ->fold([])(fn($merged, $chunk) => [...$merged, ...$chunk]);
    }

    public function calculatingFactorial(): int
    {
        return ArrayList::collect([1, 2, 3, 4, 5])
            ->fold(1)(fn($x, $y) => $x * $y);
    }

    /**
     * @param callable(int): bool $predicate
     */
    public function everyViaFold(callable $predicate): bool
    {
        return ArrayList::collect([1, 2, 3, 4, 5])
            ->fold(true)(fn($cond, $num) => $cond && $predicate($num));
    }

    /**
     * @return ArrayList<int>
     */
    public function reverse(): ArrayList
    {
        return ArrayList::collect([1, 2, 3, 4, 5])
            ->fold(ArrayList::empty())(fn($reversed, $current) => $reversed->prepended($current));
    }
}
