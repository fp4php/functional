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
final class LastMapOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $f
     * @return Option<TVO>
     */
    public function __invoke(callable $f): Option
    {
        $last = Option::none();

        foreach ($this->gen as $key => $value) {
            $res = $f($key, $value);

            if ($res->isSome()) {
                $last = $res;
            }
        }

        return $last;
    }
}
