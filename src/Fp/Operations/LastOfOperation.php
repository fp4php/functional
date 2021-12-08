<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class LastOfOperation extends AbstractOperation
{
    /**
     * @template TVO
     * @param class-string<TVO> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Option<TVO>
     */
    public function __invoke(string $fqcn, bool $invariant = false): Option
    {
        return LastOperation::of($this->gen)(fn($elem) => of($elem, $fqcn, $invariant))
            ->filterOf($fqcn, $invariant);
    }
}
