<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * ```php
 * // stack safe factorial example
 *
 * function fac(int $n): Trampoline {
 *     return $n === 0
 *         ? new Done(1)
 *         : new FlatMap(
 *             new More(fn() => fac($n - 1)),
 *             fn(int $x) => new Done($n * $x)
 *         );
 * }
 * ```
 *
 * @template A
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
                    $x = $cur->subject;
                    $f = $cur->kleisli;
                    switch (true) {
                        case $x instanceof Done:
                            $cur = $f($x->value);
                            continue 3;
                        case $x instanceof More:
                            $cur = new FlatMap(($x->resume)(), $f);
                            continue 3;
                        case $x instanceof FlatMap:
                            $y = $x->subject;
                            $g = $x->kleisli;
                            $cur = $y->flatMap(fn($q) => $g($q)->flatMap($f));
                            continue 3;
                    }
                    continue 2;
            }
        }
    }
}
