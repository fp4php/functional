<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Functional\Semigroup\Semigroup;

/**
 * @template A
 * @psalm-immutable
 * @extends Semigroup<A>
 */
abstract class Monoid extends Semigroup
{
    /**
     * @psalm-return A
     */
    abstract public function empty(): mixed;

    /**
     * @template T
     *
     * @psalm-param 'float'|'int'|'string'|'bool'|'scalar'|class-string<T> $of
     * @psalm-return (
     *     $of is 'float'      ? Monoid<list<float>>  : (
     *     $of is 'int'        ? Monoid<list<int>>    : (
     *     $of is 'string'     ? Monoid<list<string>> : (
     *     $of is 'bool'       ? Monoid<list<bool>>   : (
     *     $of is 'scalar'     ? Monoid<list<scalar>> : (
     *     $of is class-string ? Monoid<list<T>>      : (
     *     Monoid<list>
     * )))))))
     */
    public static function listInstance(?string $of = null): Monoid
    {
        return new ListMonoid();
    }
}
