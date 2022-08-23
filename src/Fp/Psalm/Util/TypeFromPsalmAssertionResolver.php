<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Closure;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use Psalm\Context;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use function Fp\Collection\at;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;

final class TypeFromPsalmAssertionResolver
{
    /**
     * @param class-string $for_class
     * @param Closure(TGenericObject, string): Union $to_negated
     * @param Closure(TGenericObject): Option<Union> $to_return_type
     */
    public static function getMethodReturnType(
        MethodReturnTypeProviderEvent $event,
        string $for_class,
        Closure $to_negated,
        Closure $to_return_type,
        string $get_method_name = 'get',
    ): null|Union {
        $stmt = $event->getStmt();
        $ctx = $event->getContext();

        return proveTrue($get_method_name === $event->getMethodNameLowercase())
            ->flatMap(fn() => self::getMethodVar($stmt))
            ->tap(fn() => self::negateAssertion($ctx, $for_class, $to_negated))
            ->flatMap(fn($called_variable) => at($ctx->vars_in_scope, $called_variable)
                ->flatMap(fn(Union $union) => PsalmApi::$types->asSingleAtomicOf(TGenericObject::class, $union))
                ->flatMap($to_return_type))
            ->get();
    }

    /**
     * @param Closure(TGenericObject, string): Union $to_negated
     * @param class-string $for_class
     */
    private static function negateAssertion(Context $context, string $for_class, Closure $to_negated): void
    {
        foreach ($context->clauses as $clause) {
            if (count($clause->possibilities) > 1) {
                continue;
            }

            foreach ($clause->possibilities as $variable => [$possibility]) {
                $reconciled = at($context->vars_in_scope, $variable)
                    ->flatMap(fn(Union $from_scope) => PsalmApi::$types->asSingleAtomicOf(TGenericObject::class, $from_scope))
                    ->filter(fn(TGenericObject $generic) => $generic->value === $for_class)
                    ->map(fn(TGenericObject $generic) => $to_negated($generic, $possibility));

                if ($reconciled->isSome()) {
                    $context->vars_in_scope[$variable] = $reconciled->get();
                }
            }
        }
    }

    /**
     * @return Option<non-empty-string>
     */
    private static function getMethodVar(Expr $expr): Option
    {
        return Option::do(function() use ($expr) {
            $method_call = yield proveOf($expr, MethodCall::class);

            return yield proveOf($method_call->var, MethodCall::class)
                ->flatMap(fn(MethodCall $call) => self::getMethodVar($call))
                ->orElse(fn() => proveOf($method_call->var, Variable::class)
                    ->flatMap(fn(Variable $variable) => proveString($variable->name))
                    ->map(fn($name) => "\${$name}"));
        });
    }
}
