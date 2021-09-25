<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class AtOperation extends AbstractOperation
{
    /**
     * @param TK $pos
     * @return Option<TV>
     */
    public function __invoke(mixed $pos): Option
    {
        $first = null;

        foreach ($this->gen as $key => $value) {
            if ($key === $pos) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
