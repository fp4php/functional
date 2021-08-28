<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use PhpParser\Node;
use Fp\Functional\Option\Option;
use function Fp\Evidence\proveTrue;

final class GetPredicateFunction
{
    /**
     * @param Node\Arg $predicate_arg
     * @return Option<Node\Expr\Closure|Node\Expr\ArrowFunction>
     */
    public static function from(Node\Arg $predicate_arg): Option
    {
        return Option::do(function() use ($predicate_arg) {
            yield proveTrue(
                $predicate_arg->value instanceof Node\Expr\Closure ||
                $predicate_arg->value instanceof Node\Expr\ArrowFunction
            );

            return $predicate_arg->value;
        });
    }
}
