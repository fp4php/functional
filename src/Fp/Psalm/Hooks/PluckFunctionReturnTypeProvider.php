<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use Fp\Psalm\PsalmTypeParam;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use ReflectionNamedType;

use function Fp\Collection\firstOf;
use function Fp\Collection\at;
use function Fp\Collection\second;
use function Fp\Evidence\proveClassString;
use function Fp\Evidence\proveTrue;
use function Fp\Reflection\getNamedTypes;
use function Fp\Reflection\getReflectionProperty;

class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            'fp\collection\pluck',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            $arg_union = yield Psalm::getFirstArgUnion($event);
            $target_union = yield self::getTypes($event);
            return new Union([new TArray([
                PsalmTypeParam::getUnionKeyTypeParam($arg_union)->getOrElse(Type::getArrayKey()),
                $target_union,
            ])]);
        })->get();
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getTypes(FunctionReturnTypeProviderEvent $event): Option
    {
        return self::getTypesForObject($event)
            ->orElse(fn() => self::getTypesForObjectLikeArray($event));
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getTypesForObject(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $fqcn = yield self::getClassName($event);
            $key = yield self::getKey($event);
            $property_reflection = yield getReflectionProperty($fqcn, $key)->toOption();
            $named_types = getNamedTypes($property_reflection);

            $type_string = yield ArrayList::collect($named_types)
                ->map(fn(ReflectionNamedType $nt) => $nt->getName())
                ->reduce(fn(string $acc, $cur) => $acc . '|' . $cur);

            return Type::parseString($type_string);
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getTypesForObjectLikeArray(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $key = yield self::getKey($event);
            $collection_union = yield Psalm::getFirstArgUnion($event);
            $collection_value_type_param = yield PsalmTypeParam::getUnionValueTypeParam($collection_union);
            $collection_value_atomic = yield Psalm::getUnionSingeAtomic($collection_value_type_param);
            yield proveTrue($collection_value_atomic instanceof TKeyedArray);
            return yield at($collection_value_atomic->properties, $key);
        });
    }

    /**
     * @psalm-return Option<class-string>
     */
    private static function getClassName(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $union = yield Psalm::getFirstArgUnion($event);
            $type_param_union = yield PsalmTypeParam::getUnionValueTypeParam($union);
            $type_param_atomic = yield Psalm::getUnionSingeAtomic($type_param_union);
            yield proveTrue($type_param_atomic instanceof TNamedObject);

            return yield proveClassString($type_param_atomic->value);
        });
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getKey(FunctionReturnTypeProviderEvent $event): Option
    {
        return second($event->getCallArgs())
            ->flatMap(fn(Arg $arg): Option => Psalm::getArgUnion($arg, $event->getStatementsSource()))
            ->flatMap(fn(Union $key) => firstOf($key->getAtomicTypes(), TLiteralString::class))
            ->map(fn(TLiteralString $literal) => $literal->value);
    }
}
