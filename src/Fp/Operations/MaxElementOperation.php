<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

use function Fp\Cast\asList;

/**
 * @psalm-immutable
 * @template TK
 * @template TV
 * @extends AbstractOperation<TK, TV>
 */
class MaxElementOperation extends AbstractOperation
{
    /**
     * @return Option<TV>
     */
    public function __invoke(): Option
    {
        $list = asList($this->gen);

        return Option::fromNullable(!empty($list) ? max($list) : null);
    }
}
