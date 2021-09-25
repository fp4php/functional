<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMapBuffer;
use Fp\Collections\Map;
use Fp\Collections\Nil;
use Fp\Collections\Seq;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 * @psalm-suppress ImpureMethodCall TODO
 */
class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @psalm-param callable(TV, TK): TKO $f
     * @psalm-return Map<TKO, Seq<TV>>
     */
    public function __invoke(callable $f): Map
    {
        $buffer = new HashMapBuffer();

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($value, $key);

            /** @var Seq<TV> $group */
            $group = $buffer->get($groupKey)->getOrElse(Nil::getInstance());

            $buffer->update($groupKey, $group->prepended($value));
        }

        return $buffer->toHashMap();
    }
}
