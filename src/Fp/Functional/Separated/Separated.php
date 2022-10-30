<?php

declare(strict_types=1);

namespace Fp\Functional\Separated;

use Fp\Collections\Collection;
use Fp\Functional\Either\Either;
use Fp\Operations\ToStringOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\SeparatedToEitherMethodReturnTypeProvider;

/**
 * @template-covariant L
 * @template-covariant R
 *
 * @psalm-seal-methods
 * @mixin SeparatedExtensions<L, R>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class Separated
{
    /**
     * @param L $left
     * @param R $right
     */
    public function __construct(
        private readonly mixed $left,
        private readonly mixed $right,
    ) {}

    /**
     * @template TLeft
     * @template TRight
     *
     * @param TLeft $left
     * @param TRight $right
     * @return Separated<TLeft, TRight>
     */
    public static function create(mixed $left, mixed $right): Separated
    {
        return new Separated($left, $right);
    }

    /**
     * Transforms the left side.
     *
     * ```php
     * >>> Separated::create(1, 2)->mapLeft(fn($i) => $i + 1);
     * => Separated(2, 2)
     * ```
     *
     * @template LO
     *
     * @param callable(L): LO $callback
     * @return Separated<LO, R>
     */
    public function mapLeft(callable $callback): Separated
    {
        return Separated::create($callback($this->left), $this->right);
    }

    /**
     * Transforms the right side.
     *
     * ```php
     * >>> Separated::create(1, 2)->map(fn($i) => $i + 1);
     * => Separated(1, 3)
     * ```
     *
     * @template RO
     *
     * @param callable(R): RO $callback
     * @return Separated<L, RO>
     */
    public function map(callable $callback): Separated
    {
        return Separated::create($this->left, $callback($this->right));
    }

    /**
     * Performs side effect on the left side.
     *
     * ```php
     * >>> Separated::create(1, 2)->tapLeft(fn($i) => print_r($i + 1));
     * => Separated(1, 2)
     * ```
     *
     * @param callable(L): void $callback
     * @return Separated<L, R>
     */
    public function tapLeft(callable $callback): Separated
    {
        $callback($this->left);
        return $this;
    }

    /**
     * Performs side effect on the right side.
     *
     * ```php
     * >>> Separated::create(1, 2)->tap(fn($i) => print_r($i + 1));
     * => Separated(1, 2)
     * ```
     *
     * @param callable(R): void $callback
     * @return Separated<L, R>
     */
    public function tap(callable $callback): Separated
    {
        $callback($this->right);
        return $this;
    }

    /**
     * Swaps left and right sides.
     *
     * ```php
     * >>> Separated(1, 2)->swap();
     * => Separated(2, 1)
     * ```
     *
     * @return Separated<R, L>
     */
    public function swap(): Separated
    {
        return new Separated($this->right, $this->left);
    }

    /**
     * ```php
     * >>> Separated(ArrayList::collect([1, 2, 3]), ArrayList::collect([4, 5, 6]))->toEither();
     * => Left(ArrayList(1, 2, 3))
     *
     * >>> Separated(ArrayList::empty(), ArrayList::collect([4, 5, 6]))->toEither();
     * => Right(ArrayList(4, 5, 6));
     *
     * >>> Separated(ArrayList::empty(), ArrayList::empty())->toEither();
     * => Right(ArrayList(4, 5, 6));
     * ```
     *
     * @template LO
     * @template RO
     * @psalm-if-this-is Separated<Collection<mixed, LO>, Collection<mixed, RO>>
     *
     * @return Either<Collection<mixed, LO>, Collection<mixed, RO>>
     *
     * @see SeparatedToEitherMethodReturnTypeProvider
     */
    public function toEither(): Either
    {
        return count($this->left) > 0
            ? Either::left($this->left)
            : Either::right($this->right);
    }

    /**
     * Returns string representation.
     *
     * ```php
     * >>> Separated(1, 2)->toString();
     * => 'Separated(1, 2)'
     * ```
     */
    public function toString(): string
    {
        return sprintf('Separated(%s, %s)',
            ToStringOperation::of($this->left),
            ToStringOperation::of($this->right));
    }

    /**
     * Returns the left side.
     *
     * ```php
     * >>> Separated(1, 2)->getLeft();
     * => 1
     * ```
     *
     * @return L
     */
    public function getLeft(): mixed
    {
        return $this->left;
    }

    /**
     * Returns the right side.
     *
     * ```php
     * >>> Separated(1, 2)->getRight();
     * => 2
     * ```
     *
     * @return R
     */
    public function getRight(): mixed
    {
        return $this->right;
    }

    /**
     * Transforms pair to tuple with two elements.
     *
     * ```php
     * >>> Separated(1, 2)->toTuple();
     * => [1, 2]
     * ```
     *
     * @return array{L, R}
     */
    public function toTuple(): array
    {
        return [$this->left, $this->right];
    }

    #region Extension

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return SeparatedExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return SeparatedExtensions::callStatic($name, $arguments);
    }

    #endregion Extension
}
