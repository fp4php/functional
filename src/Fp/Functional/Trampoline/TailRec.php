<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * ```php
 * // stack safe factorial example
 *
 * function fac(int $n): TailRec {
 *     return $n === 0
 *         ? new Returned(1)
 *         : new FlatMap(
 *             new Suspend(fn() => fac($n - 1)),
 *             fn(int $x) => new Returned($n * $x)
 *         );
 * }
 * ```
 *
 * @template A
 */
abstract class TailRec
{
    /**
     * @template B
     * @param Closure(A): B $f
     * @return TailRec<B>
     */
    public function map(Closure $f): TailRec
    {
        return $this->flatMap(function ($a) use ($f) {
            /** @var A $a */
            return new Returned($f($a));
        });
    }

    /**
     * @template B
     * @param Closure(A): TailRec<B> $f
     * @return TailRec<B>
     */
    public function flatMap(Closure $f): TailRec
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
                case $cur instanceof Returned:
                    /** @var A */
                    return $cur->value;
                case $cur instanceof Suspend:
                    $cur = ($cur->resume)();
                    continue 2;
                case $cur instanceof FlatMap:
                    $x = $cur->subject;
                    $f = $cur->kleisli;
                    switch (true) {
                        case $x instanceof Returned:
                            $cur = $f($x->value);
                            continue 3;
                        case $x instanceof Suspend:
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
