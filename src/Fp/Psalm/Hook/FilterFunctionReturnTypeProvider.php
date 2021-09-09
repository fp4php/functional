<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook;

use Fp\Psalm\Psalm;
use Fp\Psalm\TypeRefinement\CollectionTypeExtractor;
use Psalm\Type;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Fp\Psalm\TypeRefinement\RefineByPredicate;
use Fp\Psalm\TypeRefinement\RefinementContext;
use Fp\Psalm\TypeRefinement\RefinementResult;
use Fp\Functional\Option\Option;

use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class FilterFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Collection\filter')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Type\Union
    {
        $reconciled = Option::do(function() use ($event) {
            $source = yield proveOf($event->getStatementsSource(), StatementsAnalyzer::class);

            $call_args = $event->getCallArgs();
            yield proveTrue(count($call_args) >= 2);

            $collection_type_params = yield Psalm::getFirstArgUnion($event)
                ->flatMap([CollectionTypeExtractor::class, 'extract']);

            $predicate = yield Psalm::getArgFunctionLike($call_args[1]);

            $refinement_context = new RefinementContext(
                refine_for: 'fp\collection\filter',
                predicate: $predicate,
                execution_context: $event->getContext(),
                codebase: $source->getCodebase(),
                source: $source,
            );

            $result = RefineByPredicate::for(
                $refinement_context,
                $collection_type_params,
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

    /**
     * @psalm-return Option<Type\Union>
     */
    private static function getReturnType(FunctionReturnTypeProviderEvent $event, RefinementResult $result): Option
    {
        $call_args = $event->getCallArgs();

        // $preserveKeys true by default
        if (3 !== count($call_args)) {
            return Option::some(self::listType($result));
        }

        return Psalm::getArgUnion($call_args[2], $event->getStatementsSource())
            ->flatMap(fn($type) => Psalm::getUnionSingeAtomic($type))
            ->map(fn($preserve_keys) => $preserve_keys::class === Type\Atomic\TFalse::class
                ? self::listType($result)
                : self::arrayType($result));
    }
}
