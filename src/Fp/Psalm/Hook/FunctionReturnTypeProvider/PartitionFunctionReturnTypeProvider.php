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
use Fp\PsalmToolkit\PsalmApi;
use PhpParser\Node\Arg;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Evidence\of;
use function Fp\Callable\ctor;
use function Fp\Cast\asNonEmptyArray;
use function Fp\Collection\at;
use function Fp\Collection\head;
use function Fp\Collection\sequenceOptionT;
use function Fp\Collection\tail;
use function Fp\Evidence\proveOf;

final class PartitionFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\partitionT'),
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
                    ->traverseOption(fn(int $offset) => sequenceOptionT(
                        fn() => Option::some(RefineForEnum::Value),
                        fn() => at($args, $offset)->pluck('value')->flatMap(of(FunctionLike::class)),
                        fn() => Option::some($event->getContext()),
                        fn() => proveOf($event->getStatementsSource(), StatementsAnalyzer::class),
                        fn() => Option::some($type_params),
                    ))
                    ->map(function(ArrayList $args) use ($type_params) {
                        $init_types = $args
                            ->mapN(ctor(RefinementContext::class))
                            ->map(RefineByPredicate::for(...))
                            ->map(fn(CollectionTypeParams $params) => $params->val_type);

                        $last_type = $init_types
                            ->fold($type_params->val_type)(
                                function(Union $last, Union $current) {
                                    $builder = $last->getBuilder();
                                    $builder->removeType($current->getId());

                                    return $builder->freeze();
                                },
                            );

                        return $init_types
                            ->appended($last_type)
                            ->map(fn(Union $type) => [Type::getListAtomic($type)])
                            ->map(ctor(Union::class))
                            ->toList();
                    })
            )
            ->flatMap(fn(array $partitions) => asNonEmptyArray($partitions))
            ->map(function(array $non_empty_partitions) {
                return new Union([
                    new TKeyedArray($non_empty_partitions, is_list: true),
                ]);
            })
            ->get();
    }
}
