<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeAssertion;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Fp\Functional\Option\Option;

use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;

final class TypeAssertion
{
    /**
     * Turns known pseudo ADT to union.
     * For example Either<L, R> to Left<L> | Right<R>
     * or Option<T> to None | Some<T>.
     *
     * @psalm-param non-empty-array<string> $assertion_methods
     */
    public static function changeTypeAfterAssertionCall(
        AfterMethodCallAnalysisEvent $event,
        PseudoAdtToUnion $pseudo_adt_to_union,
        array $assertion_methods,
    ): void
    {
        Option::do(function() use ($event, $pseudo_adt_to_union, $assertion_methods) {
            $context = $event->getContext();
            $source = $event->getStatementsSource();
            $type_provider = $source->getNodeTypeProvider();

            $assertion_method = yield self::getAssertionMethodCall($event->getExpr(), $assertion_methods);
            $variable_name = yield self::getVariableName($assertion_method);

            $adt_union = yield $pseudo_adt_to_union->getUnion($type_provider, $assertion_method);

            $type_provider->setType($assertion_method->var, $adt_union);
            $context->vars_in_scope[$variable_name] = $adt_union;
        });
    }

    /**
     * @psalm-param non-empty-array<string> $assertion_methods
     * @psalm-return Option<MethodCall>
     */
    private static function getAssertionMethodCall(Expr $expr, array $assertion_methods): Option
    {
        return Option::do(function() use ($expr, $assertion_methods) {
            $method_call = yield proveOf($expr, MethodCall::class);
            $method_identifier = yield proveOf($method_call->name, Identifier::class);

            yield proveTrue(
                in_array($method_identifier->name, $assertion_methods, true)
            );

            return $method_call;
        });
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getVariableName(MethodCall $method_call): Option
    {
        return Option::do(function() use ($method_call) {
            $variable = yield proveOf($method_call->var, Variable::class);
            $name = yield proveString($variable->name);

            return '$' . $name;
        });
    }
}
