<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use function Fp\Cast\asList;
use function Fp\Collection\first;
use function Fp\Collection\keys;
use function Fp\Collection\second;
use function Fp\Collection\traverseOption;

final class SequenceEitherFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceEither'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        $type = Option::do(function() use ($event) {
            $types = yield PsalmApi::$args->getCallArgs($event)
                ->flatMap(fn($args) => $args->head())
                ->flatMap(fn(CallArg $arg) => PsalmApi::$types->asSingleAtomicOf(TKeyedArray::class, $arg->type));

            $left_cases = yield traverseOption(
                asList($types->properties),
                fn(Union $type) => PsalmApi::$types
                    ->asSingleAtomicOf(TGenericObject::class, $type)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Either::class)
                    ->flatMap(fn(TGenericObject $option) => first($option->type_params))
            );

            $mapped = yield traverseOption(
                $types->properties,
                fn(Union $type) => PsalmApi::$types
                    ->asSingleAtomicOf(TGenericObject::class, $type)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Either::class)
                    ->flatMap(fn(TGenericObject $option) => second($option->type_params))
            );

            $is_list = range(0, count($mapped) - 1) === keys($mapped);

            $keyed = new TKeyedArray($mapped);
            $keyed->is_list = $is_list;
            $keyed->sealed = $is_list;

            return new Union([
                new TGenericObject(Either::class, [
                    Type::combineUnionTypeArray($left_cases, PsalmApi::$codebase),
                    new Union([$keyed]),
                ]),
            ]);
        });

        return $type->get();
    }
}
