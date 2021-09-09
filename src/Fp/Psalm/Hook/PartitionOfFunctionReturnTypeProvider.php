<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\PSL;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TLiteralClassString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Evidence\proveNonEmptyArrayOf;

class PartitionOfFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\partitionOf'),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            $source = $event->getStatementsSource();
            $collection_union = yield PSL::getFirstArgUnion($event);
            $collection_value_type_param = yield PSL::getUnionValueTypeParam($collection_union);

            $partitions = ArrayList::collect($event->getCallArgs())
                ->drop(2)
                ->filterMap(fn(Arg $arg) => PSL::getArgSingleAtomic($arg, $source))
                ->filter(fn(Atomic $a) => $a instanceof TLiteralClassString)
                ->map(fn(TLiteralClassString $cs) => new TNamedObject($cs->value))
                ->map(fn(TNamedObject $no) => new TList(new Union([$no])))
                ->appended(new TList($collection_value_type_param))
                ->map(fn(TList $list) => new Union([$list]))
                ->toArray();

            $tuple = new TKeyedArray(yield proveNonEmptyArrayOf($partitions, Union::class));
            $tuple->is_list = true;

            return new Union([$tuple]);
        })->get();
    }
}
