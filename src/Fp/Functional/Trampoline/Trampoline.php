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
                    $x = $cur->sub;
                    $f = $cur->cont;
                    switch (true) {
                        case $x instanceof Done:
                            $cur = $f($x->value);
                            continue 3;
                        case $x instanceof More:
                            $cur = new FlatMap(($x->resume)(), $f);
                            continue 3;
                        case $x instanceof FlatMap:
                            $y = $x->sub;
                            $g = $x->cont;
                            $cur = $y->flatMap(fn($q) => $g($q)->flatMap($f));
                            continue 3;
                    }
                    continue 2;
            }
        }
    }
}
