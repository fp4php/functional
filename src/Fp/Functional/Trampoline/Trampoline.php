<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @psalm-immutable
 * @template-covariant A
 */
abstract class Trampoline
{
    /**
     * @template B
     * @param Closure(A): B $f
     * @return Trampoline<B>
     */
    public function map(Closure $f): Trampoline
    {
        return $this->flatMap(function ($a) use ($f) {
            /** @var A $a */
            return new Done($f($a));
        });
    }

    /**
     * @template B
     * @param Closure(A): Trampoline<B> $f
     * @return Trampoline<B>
     */
    public function flatMap(Closure $f): Trampoline
    {
        return new FlatMap($this, $f);
    }

    /**
     * @return A
     */
    public function run(): mixed
    {
        $cur = $this;

        while(true) {
            switch (true) {
                case $cur instanceof Done:
                    /** @var A */
                    return $cur->value;
                case $cur instanceof More:
                    $cur = ($cur->resume)();
                    continue 2;
                case $cur instanceof FlatMap:
                    $sub1 = $cur->sub;
                    $cont1 = $cur->cont;
                    switch (true) {
                        case $sub1 instanceof Done:
                            $cur = $cont1($sub1->value);
                            continue 3;
                        case $sub1 instanceof More:
                            $cur = new FlatMap(($sub1->resume)(), $cont1);
                            continue 3;
                        case $sub1 instanceof FlatMap:
                            $sub2 = $sub1->sub;
                            $cont2 = $sub1->cont;

                            // Reassociate the bind to the right
                            $cur = $sub2->flatMap(fn($z) => $cont2($z)->flatMap($cont1));

                            continue 3;
                    }
                    continue 2;
            }
        }
    }
}
