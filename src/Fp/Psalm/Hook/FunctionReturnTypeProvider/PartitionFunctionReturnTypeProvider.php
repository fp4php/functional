<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Psalm\Util\PSL;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\Cast\asNonEmptyArray;
use function Fp\Collection\head;
use function Fp\Collection\map;
use function Fp\Collection\tail;

class PartitionFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\partition'),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        $source = $event->getStatementsSource();
        $args = $event->getCallArgs();
        $partition_count = count(tail($args));

        return head($args)
            ->flatMap(fn(Arg $head_arg) => PSL::getArgUnion($head_arg, $source))
            ->map(function(Union $head_arg_type) use ($partition_count) {
                $atomic_types = map(
                    $head_arg_type->getAtomicTypes(),
                    fn(Atomic $a) => match (true) {
                        ($a instanceof TArray) => new TList($a->type_params[1]),
                        ($a instanceof TKeyedArray) => new TList($a->getGenericValueType()),
                        ($a instanceof TList) => new TList($a->type_param),
                        default => $a
                    }
                );

                $upper_union = new Union([
                    ...array_values($atomic_types),
                    ...array_values($head_arg_type->getTemplateTypes()),
                    ...array_values($head_arg_type->getClosureTypes()),
                    ...array_values($head_arg_type->getCallableTypes()),
                ]);

                return map(
                    range(1, $partition_count + 1),
                    fn() => clone $upper_union
                );
            })
            ->flatMap(fn(array $partitions) => asNonEmptyArray($partitions))
            ->map(function (array $non_empty_partitions) {
                $tuple = new TKeyedArray($non_empty_partitions);
                $tuple->is_list = true;
                return new Union([$tuple]);
            })
            ->get();
    }
}
