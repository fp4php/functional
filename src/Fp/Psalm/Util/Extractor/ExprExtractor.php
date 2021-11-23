<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
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

    /**
     * @return Option<ArrayList<Arg>>
     */
    public static function getCallArgs(FuncCall|MethodCall|StaticCall $call): Option
    {
        return ArrayList::collect($call->args)
            ->everyMap(fn($arg) => $arg instanceof Arg ? Option::some($arg) : Option::none());
    }
}
