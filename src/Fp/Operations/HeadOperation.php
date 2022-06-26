<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class HeadOperation extends AbstractOperation
{
    /**
     * @return Option<TV>
     */
    public function __invoke(): Option
    {
        return FirstOperation::of($this->gen)();
    }
}
