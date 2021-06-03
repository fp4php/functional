<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Functional\Monoid\ListMonoid;
use Fp\Functional\Validated\Validated;

/**
 * @template A
 * @psalm-immutable
 */
abstract class Semigroup
{
    /**
     * @psalm-pure
     * @psalm-param A $lhs
     * @psalm-param A $rhs
     *
     * @psalm-return A
     */
    abstract public function combine(mixed $lhs, mixed $rhs): mixed;

    /**
     * @psalm-param non-empty-list<A> $elements
     * @psalm-return A
     */
    public function combineAll(array $elements): mixed
    {
        $acc = array_shift($elements);

        foreach ($elements as $element) {
            $acc = $this->combine($acc, $element);
        }

        return $acc;
    }

    /**
     * @template T
     *
     * @psalm-param 'float'|'int'|'string'|'bool'|'scalar'|class-string<T> $of
     * @psalm-return (
     *     $of is 'float'      ? Semigroup<non-empty-list<float>>  : (
     *     $of is 'int'        ? Semigroup<non-empty-list<int>>    : (
     *     $of is 'string'     ? Semigroup<non-empty-list<string>> : (
     *     $of is 'bool'       ? Semigroup<non-empty-list<bool>>   : (
     *     $of is 'scalar'     ? Semigroup<non-empty-list<scalar>> : (
     *     $of is class-string ? Semigroup<non-empty-list<T>>      : (
     *     Semigroup<non-empty-list>
     * )))))))
     */
    public static function nonEmptyListInstance(string $of): Semigroup
    {
        return new NonEmptyListSemigroup();
    }

    /**
     * @template T
     *
     * @psalm-param 'float'|'int'|'string'|'bool'|'scalar'|class-string<T> $of
     * @psalm-return (
     *     $of is 'float'      ? Semigroup<list<float>>  : (
     *     $of is 'int'        ? Semigroup<list<int>>    : (
     *     $of is 'string'     ? Semigroup<list<string>> : (
     *     $of is 'bool'       ? Semigroup<list<bool>>   : (
     *     $of is 'scalar'     ? Semigroup<list<scalar>> : (
     *     $of is class-string ? Semigroup<list<T>>      : (
     *     Semigroup<list>
     * )))))))
     */
    public static function listInstance(string $of): Semigroup
    {
        return new ListMonoid();
    }

    /**
     * @template AA
     * @template EE
     *
     * @psalm-param Semigroup<AA> $validSemigroup
     * @psalm-param Semigroup<EE> $invalidSemigroup
     *
     * @psalm-return Semigroup<Validated<EE, AA>>
     */
    public static function validatedInstance(
        Semigroup $validSemigroup,
        Semigroup $invalidSemigroup
    ): Semigroup
    {
        return new ValidatedSemigroup($validSemigroup, $invalidSemigroup);
    }
}
