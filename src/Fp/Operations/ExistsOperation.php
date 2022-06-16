<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class ExistsOperation extends AbstractOperation
{
    /**
     * @psalm-param callable(TV, TK): bool $f
     */
    public function __invoke(callable $f): bool
    {
        $exists = false;

        foreach ($this->gen as $key => $value) {
            if ($f($value, $key)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }
}
