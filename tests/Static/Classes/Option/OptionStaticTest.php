<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;

final class OptionStaticTest
{
    /**
     * @return Option<int>
     */
    public function testCreation(?int $in): Option
    {
        return Option::fromNullable($in);
    }

    /**
     * @param Option<int> $in
     * @return int|null
     */
    public function testGet(Option $in): ?int
    {
        return $in->get();
    }

    /**
     * @psalm-return '1'|null
     */
    public function testMap(): ?string
    {
        return Option::fromNullable(1)
            ->map(fn(int $v) => (string) $v)
            ->get();
    }

    /**
     * @psalm-return '1'|null
     */
    public function testFlatMap(): ?string
    {
        return Option::fromNullable(1)
            ->flatMap(fn(int $v) => Option::fromNullable((string) $v))
            ->get();
    }

    /**
     * @return ArrayList<1>
     */
    public function testToArrayList(): ArrayList
    {
        return Option::fromNullable(1)
            ->toArrayList(fn(int $v) => ArrayList::singleton($v));
    }
}
