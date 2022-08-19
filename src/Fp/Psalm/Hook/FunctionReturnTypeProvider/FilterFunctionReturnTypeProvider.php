<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
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
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;

final class FilterFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    private const COLLECTION_IDX = 0;
    private const PREDICATE_IDX = 1;
    private const PRESERVE_KEY_IDX = 2;

    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\filter'),
            strtolower('Fp\Collection\filterKV'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => sequenceOption([
                Option::some($event->getFunctionId() === 'fp\collection\filterkv'
                    ? RefinementContext::FILTER_KEY_VALUE
                    : RefinementContext::FILTER_VALUE),
                $args->at(self::PREDICATE_IDX)
                    ->map(fn(CallArg $arg) => $arg->node->value)
                    ->filterOf(FunctionLike::class),
                Option::some($event->getContext()),
                proveOf($event->getStatementsSource(), StatementsAnalyzer::class),
                $args->at(self::COLLECTION_IDX)
                    ->map(fn(CallArg $arg) => $arg->type)
                    ->flatMap(GetCollectionTypeParams::keyValue(...)),
            ]))
            ->map(fn($args) => new RefinementContext(...$args))
            ->map(RefineByPredicate::for(...))
            ->map(fn(CollectionTypeParams $result) => self::getReturnType($event, $result))
            ->get();
    }

    private static function getReturnType(FunctionReturnTypeProviderEvent $event, CollectionTypeParams $result): Union
    {
        return at($event->getCallArgs(), self::PRESERVE_KEY_IDX)
            ->flatMap(fn(Arg $preserve_keys) => PsalmApi::$args->getArgType($event, $preserve_keys))
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->map(fn($preserve_keys) => $preserve_keys::class === TFalse::class
                ? self::listType($result)
                : self::arrayType($result))
            ->getOrCall(fn() => self::listType($result));
    }

    private static function arrayType(CollectionTypeParams $result): Union
    {
        return new Union([
            new TArray([$result->key_type, $result->val_type]),
        ]);
    }

    private static function listType(CollectionTypeParams $result): Union
    {
        return new Union([
            new TList($result->val_type),
        ]);
    }
}
