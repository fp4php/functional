<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

/**
 * @internal
 */
trait ExprExtractor
{
    /**
     * @psalm-return Option<FunctionLike>
     */
    public static function getArgFunctionLike(Arg $predicate_arg): Option
    {
        return Option::some($predicate_arg)
            ->map(fn(Arg $arg) => $arg->value)
            ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);
    }
}
