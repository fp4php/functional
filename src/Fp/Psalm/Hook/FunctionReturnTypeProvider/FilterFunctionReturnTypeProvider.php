<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\FunctionLike;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TFalse;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Fp\Functional\Option\Option;

use function Fp\Collection\at;
use function Fp\Evidence\proveOf;

final class FilterFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\filter'),
            strtolower('Fp\Collection\filterKV'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        $reconciled = Option::do(function() use ($event) {
            $source = yield proveOf($event->getStatementsSource(), StatementsAnalyzer::class);

            $call_args = yield PsalmApi::$args->getCallArgs($event);

            $collection_type_params = yield $call_args->at(0)
                ->flatMap(fn(CallArg $arg) => GetCollectionTypeParams::keyValue($arg->type));

            $predicate = yield $call_args->at(1)
                ->map(fn(CallArg $arg) => $arg->node->value)
                ->filterOf(FunctionLike::class);

            $refinement_context = new RefinementContext(
                refine_for: $event->getFunctionId() === 'fp\collection\filterkv'
                    ? RefinementContext::FILTER_KEY_VALUE
                    : RefinementContext::FILTER_VALUE,
                predicate: $predicate,
                execution_context: $event->getContext(),
                source: $source,
            );

            $result = RefineByPredicate::for(
                $refinement_context,
                $collection_type_params,
            );

            return self::getReturnType($event, $result);
        });

        return $reconciled->get();
    }

    private static function arrayType(CollectionTypeParams $result): Union
    {
        return new Union([
            new TArray([
                $result->key_type,
                $result->val_type,
            ]),
        ]);
    }

    private static function listType(CollectionTypeParams $result): Union
    {
        return new Union([
            new TList($result->val_type),
        ]);
    }

    private static function getReturnType(FunctionReturnTypeProviderEvent $event, CollectionTypeParams $result): Union
    {
        return at($event->getCallArgs(), 2)
            ->flatMap(fn(Arg $preserve_keys) => PsalmApi::$args->getArgType($event, $preserve_keys))
            ->flatMap(fn($type) => PsalmApi::$types->asSingleAtomic($type))
            ->map(fn($preserve_keys) => $preserve_keys::class === TFalse::class
                ? self::listType($result)
                : self::arrayType($result))
            ->getOrCall(fn() => self::listType($result));
    }
}
