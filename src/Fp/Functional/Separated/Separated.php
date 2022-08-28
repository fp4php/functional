<?php

declare(strict_types=1);

namespace Fp\Functional\Separated;

use Fp\Collections\Collection;
use Fp\Functional\Either\Either;
use Fp\Operations\ToStringOperation;

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
     * @template LO
     * @template RO
     * @psalm-if-this-is Separated<Collection<LO>, Collection<RO>>
     *
     * @return Either<Collection<LO>, Collection<RO>>
     */
    public function toEither(): Either
    {
        return count($this->left) > 0
            ? Either::left($this->left)
            : Either::right($this->right);
    }

    public function toString(): string
    {
        return sprintf('Separated(%s, %s)',
            ToStringOperation::of($this->left),
            ToStringOperation::of($this->right));
    }

    /**
     * @return array{L, R}
     */
    public function toTuple(): array
    {
        return [$this->left, $this->right];
    }
}
