<?php

declare(strict_types=1);

namespace Fp\Functional\AndThen;

use Closure;
use Error;

/**
 * @psalm-immutable
 * @template T
 * @template-covariant R
 */
abstract class AndThen
{
    protected const FUSION_MAX_STACK_DEPTH = 128;

    /**
     * @template RO
     * @psalm-param (callable(R): RO)|AndThen<R, RO> $cont
     * @return AndThen<T, RO>
     * @psalm-suppress all
     */
    public function andThen(callable|AndThen $cont): AndThen
    {
        if ($cont instanceof AndThen) {
            return $this->then($this, $cont);
        }

        return match (true) {
            $this instanceof Single && $this->index < self::FUSION_MAX_STACK_DEPTH => Single::of(
                fn($in) => $cont(($this->func)($in)),
                $this->index + 1
            ),
            $this instanceof Concat
                && $this->right instanceof Single
                && $this->right->index < self::FUSION_MAX_STACK_DEPTH => Concat::of(
                    $this->left,
                    Single::of(
                        fn($in) => $cont(($this->right->func)($in)),
                        $this->right->index + 1
                    )
                ),
            default => Concat::of($this, Single::of($cont, 0))
        };
    }

    /**
     * @psalm-pure
     * @template A
     * @template B
     * @template C
     * @param AndThen<A, B> $ab
     * @param AndThen<B, C> $bc
     * @return AndThen<A, C>
     * @psalm-suppress all
     */
    private function then(AndThen $ab, AndThen $bc): AndThen
    {
        if ($ab instanceof Single) {
            if ($bc instanceof Single) {
                return $ab->index + $bc->index < self::FUSION_MAX_STACK_DEPTH
                    ? Single::of(fn($in) => ($bc->func)(($ab->func)($in)), $ab->index + $bc->index + 1)
                    : Concat::of($ab, $bc);

            } elseif ($bc instanceof Concat
                && $bc->left instanceof Single
                && $ab->index + $bc->left->index < self::FUSION_MAX_STACK_DEPTH) {

                return Concat::of(
                    Single::of(
                        fn($in) => ($bc->left->func)(($ab->func)($in)),
                        $ab->index + $bc->left->index + 1
                    ),
                    $bc->right
                );
            } else {
                return Concat::of($ab, $bc);
            }
        } elseif ($ab instanceof Concat && $ab->right instanceof Single) {
            if ($bc instanceof Single) {
                return $ab->right->index + $bc->index < self::FUSION_MAX_STACK_DEPTH
                    ? Concat::of($ab->left, Single::of(
                        fn($in) => ($bc->func)(($ab->right->func)($in)),
                        $ab->right->index + $bc->index + 1
                    ))
                    : Concat::of($ab, $bc);
            } elseif ($bc instanceof Concat
                && $bc->left instanceof Single
                && $ab->right->index + $bc->left->index < self::FUSION_MAX_STACK_DEPTH) {

                return Concat::of(
                    $ab->left,
                    Concat::of(
                        Single::of(
                            fn($in) => ($bc->left->func)(($ab->right->func)($in)),
                            $ab->right->index + $bc->left->index + 1
                        ),
                        $bc->right
                    )
                );
            }
        } else {
            return Concat::of($ab, $bc);
        }
    }

    /**
     * @psalm-pure
     * @template AA
     * @template BB
     * @param Closure(AA): BB $fun
     * @return AndThen<AA, BB>
     */
    public static function apply(Closure $fun): self
    {
        return Single::of($fun, 0);
    }

    /**
     * @param T $in
     * @return R
     * @psalm-suppress all
     */
    public function run(mixed $in): mixed
    {
        $self = $this;
        $current = $in;

        while(true) {
            switch (true) {
                case $self instanceof Single:
                    /** @var R */
                    return ($self->func)($current);
                case $self instanceof Concat && $self->left instanceof Single:
                    $current = ($self->left->func)($current);
                    $self = $self->right;
                    continue 2;
                case $self instanceof Concat && $self->left instanceof Concat:
                    $self = $self->left->rotateAccum($self->right);
                    continue 2;
            }
        }
    }

    public function rotateAccum(AndThen $right): AndThen
    {
        $left = $this;

        while(true) {
            switch (true) {
                case $left instanceof Concat:
                    $right = Concat::of($left->right, $right);
                    $left = $left->left;
                    continue 2;
                default:
                    return Concat::of($left, $right);
            }
        }

    }
}
