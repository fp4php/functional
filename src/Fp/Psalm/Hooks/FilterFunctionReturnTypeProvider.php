<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Psalm\Type;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Fp\Psalm\TypeRefinement\RefineByPredicate;
use Fp\Psalm\TypeRefinement\RefinementContext;
use Fp\Psalm\TypeRefinement\RefinementResult;
use Fp\Functional\Option\Option;

use function Fp\Cast\asList;
use function Fp\Collection\firstOf;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class FilterFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return ['fp\collection\filter'];
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $reconciled = Option::do(function() use ($event) {
            $source = yield proveOf($event->getStatementsSource(), StatementsAnalyzer::class);

            $call_args = $event->getCallArgs();
            yield proveTrue(count($call_args) >= 2);

            $result = yield RefineByPredicate::for(
                new RefinementContext(
                    collection_arg: $call_args[0],
                    predicate_arg: $call_args[1],
                    execution_context: $event->getContext(),
                    codebase: $source->getCodebase(),
                    provider: $source->getNodeTypeProvider(),
                    source: $source,
                )
            );

            return yield self::getReturnType($event, $result);
        });

        return $reconciled->get();
    }

    private static function arrayType(RefinementResult $result): Type\Union
    {
        return new Type\Union([
            new Type\Atomic\TArray([
                $result->collection_key_type,
                $result->collection_value_type,
            ]),
        ]);
    }

    private static function listType(RefinementResult $result): Type\Union
    {
        return new Type\Union([
            new Type\Atomic\TList($result->collection_value_type),
        ]);
    }

    private static function getReturnType(FunctionReturnTypeProviderEvent $event, RefinementResult $result): Option
    {
        return Option::do(function() use ($event, $result) {
            $call_args = $event->getCallArgs();

            // $preserveKeys true by default
            if (3 !== count($call_args)) {
                return self::listType($result);
            }

            $preserve_keys_type = yield Option::fromNullable(
                $event
                    ->getStatementsSource()
                    ->getNodeTypeProvider()
                    ->getType($call_args[2]->value)
            );

            $atomics = asList($preserve_keys_type->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            return yield firstOf($atomics, Type\Atomic\TBool::class)
                ->map(fn($preserve_keys) => $preserve_keys::class === Type\Atomic\TFalse::class
                    ? self::listType($result)
                    : self::arrayType($result));
        });
    }
}
