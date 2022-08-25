<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefineForEnum;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Cast\asNonEmptyArray;
use function Fp\Collection\at;
use function Fp\Collection\head;
use function Fp\Collection\sequenceOption;
use function Fp\Collection\tail;
use function Fp\Evidence\proveOf;

final class PartitionNFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\partitionN'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        $args = $event->getCallArgs();
        $partition_count = count(tail($args));

        return head($args)
            ->flatMap(fn(Arg $head_arg) => PsalmApi::$args->getArgType($event, $head_arg))
            ->flatMap(GetCollectionTypeParams::keyValue(...))
            ->flatMap(
                fn(CollectionTypeParams $type_params) => ArrayList::range(1, $partition_count + 1)
                    ->traverseOption(fn(int $offset) => sequenceOption([
                        fn() => Option::some(RefineForEnum::Value),
                        fn() => at($args, $offset)->pluck('value')->filterOf(FunctionLike::class),
                        fn() => Option::some($event->getContext()),
                        fn() => proveOf($event->getStatementsSource(), StatementsAnalyzer::class),
                        fn() => Option::some($type_params),
                    ]))
                    ->map(fn(ArrayList $args) => $args
                        ->mapN(ctor(RefinementContext::class))
                        ->map(RefineByPredicate::for(...))
                        ->map(fn(CollectionTypeParams $params) => new Union(
                            [new TList($params->val_type)],
                        ))
                        ->appended(new Union(
                            [new TList($type_params->val_type)],
                        ))
                        ->toList())
            )
            ->flatMap(fn(array $partitions) => asNonEmptyArray($partitions))
            ->map(function(array $non_empty_partitions) {
                $tuple = new TKeyedArray($non_empty_partitions);
                $tuple->is_list = true;
                $tuple->sealed = true;

                return new Union([$tuple]);
            })
            ->get();
    }
}
