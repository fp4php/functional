<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveUnion;
use function Fp\Evidence\union;
use function Fp\Evidence\unionT;

final class ProveUnionStaticTest
{
    /**
     * @return Option<int|string>
     */
    public function proveUnion(mixed $value): Option
    {
        return proveUnion($value, [
            proveInt(...),
            proveString(...),
        ]);
    }

    /**
     * @return Option<int|string>
     */
    public function union(mixed $value): Option
    {
        return union([
            proveInt(...),
            proveString(...),
        ])($value);
    }

    /**
     * @return Option<int|string>
     */
    public function unionT(mixed $value): Option
    {
        return unionT(proveInt(...), proveString(...))($value);
    }
}
