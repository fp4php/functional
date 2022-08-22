<?php

declare(strict_types=1);

namespace Fp\Operations;

use function Fp\Cast\asList;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class DropRightOperation extends AbstractOperation
{
    /**
     * @return list<TV>
     */
    public function __invoke(int $length): array
    {
        $list = asList($this->gen);

        for ($i = 0; $i < $length; $i++) {
            array_pop($list);
        }

        return $list;
    }
}
