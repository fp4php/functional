<?php

declare(strict_types=1);

namespace Fp\Functional\AndThen;

use Closure;
use LogicException;

/**
 * @psalm-immutable
 * @template T
 * @template-covariant R
 * @psalm-suppress all
 */
abstract class AndThen
{
    protected const FUSION_MAX_STACK_DEPTH = 128;

    /**
     * @psalm-pure
     * @template A
     * @template B
     * @template C
     * @param self<A, B> $ab
     * @param self<B, C> $bc
     * @return self<A, C>
     */
    private function then(self $ab, self $bc): self
    {
        if ($ab instanceof Single) {
            if ($bc instanceof Single) {
                return $ab->index + $bc->index < self::FUSION_MAX_STACK_DEPTH
                    ? Single::of(fn($in) => ($bc->run)(($ab->run)($in)), $ab->index + $bc->index + 1)
                    : Concat::of($ab, $bc);

            } elseif ($bc instanceof Concat
                && $bc->left instanceof Single
                && $ab->index + $bc->left->index < self::FUSION_MAX_STACK_DEPTH) {

                return Concat::of(
                    Single::of(
                        fn($in) => ($bc->left->run)(($ab->run)($in)),
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
                        fn($in) => ($bc->run)(($ab->right->run)($in)),
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
                            fn($in) => ($bc->left->run)(($ab->right->run)($in)),
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
     * @return self<AA, BB>
     */
    public static function apply(Closure $fun): self
    {
        return Single::of($fun, 0);
    }

    /**
     * @template RO
     * @psalm-param (callable(R): RO)|AndThen<R, RO> $cont
     * @return self<T, RO>
     */
    public function andThen(callable|self $cont): self
    {
        if ($cont instanceof self) {
            return $this->then($this, $cont);
        }

        if ($this instanceof Single && $this->index < self::FUSION_MAX_STACK_DEPTH) {
            return Single::of(
                fn($in) => $cont(($this->run)($in)),
                $this->index + 1
            );
        } elseif ($this instanceof Concat
            && $this->right instanceof Single
            && $this->right->index < self::FUSION_MAX_STACK_DEPTH) {
            return Concat::of(
                $this->left,
                Single::of(
                    fn($in) => $cont(($this->right->run)($in)),
                    $this->right->index + 1
                )
            );
        } else {
            return Concat::of($this, Single::of($cont, 0));
        }
    }

    /**
     * @param T $in
     * @return R
     */
    public function run(mixed $in): mixed
    {
        $self = $this;
        $current = $in;

        while(!($self instanceof Single)) {
            if ($self instanceof Concat && $self->left instanceof Single) {
                $current = ($self->left->run)($current);
                $self = $self->right;
            } elseif ($self instanceof Concat && $self->left instanceof Concat) {
                $self = $self->left->rotateAccum($self->right);
            } else {
                throw new LogicException();
            }
        }

        /** @var R */
        return ($self->run)($current);
    }

    public function rotateAccum(self $rhs): self
    {
        $lhs = $this;

        while($lhs instanceof Concat) {
            $rhs = Concat::of($lhs->right, $rhs);
            $lhs = $lhs->left;
        }

        return Concat::of($lhs, $rhs);
    }
}
