<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Storage\PropertyStorage;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Collection\at;
use function Fp\Collection\firstOf;
use function Fp\Collection\second;

class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\pluck'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);
            $arg_union = yield $args->head()->map(fn(CallArg $arg) => $arg->type);

            $target_union = yield self::getTypes($event);

            return new Union([
                new TArray([
                    GetCollectionTypeParams::key($arg_union)->getOrElse(Type::getArrayKey()),
                    $target_union,
                ])
            ]);
        })->get();
    }

    /**
     * @return Option<Union>
     */
    private static function getTypes(FunctionReturnTypeProviderEvent $event): Option
    {
        return self::getTypesForObject($event)
            ->orElse(fn() => self::getTypesForObjectLikeArray($event));
    }

    /**
     * @return Option<Union>
     */
    private static function getTypesForObject(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $class = yield self::getClassStorage($event);
            $key = yield self::getKey($event);

            return yield at($class->properties, $key)
                ->map(fn(PropertyStorage $property) => $property->type ?? Type::getMixed());
        });
    }

    /**
     * @return Option<Union>
     */
    private static function getTypesForObjectLikeArray(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);
            $key = yield self::getKey($event);

            return yield $args->head()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(fn(Union $type) => GetCollectionTypeParams::value($type))
                ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TKeyedArray::class, $type))
                ->flatMap(fn(TKeyedArray $array) => at($array->properties, $key));
        });
    }

    /**
     * @return Option<ClassLikeStorage>
     */
    private static function getClassStorage(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);

            return yield $args->head()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(fn(Union $type) => GetCollectionTypeParams::value($type))
                ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TNamedObject::class, $type))
                ->flatMap(fn(TNamedObject $object) => PsalmApi::$classlikes->getStorage($object));
        });
    }

    /**
     * @return Option<string>
     */
    private static function getKey(FunctionReturnTypeProviderEvent $event): Option
    {
        return second($event->getCallArgs())
            ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
            ->flatMap(fn(Union $key) => firstOf($key->getAtomicTypes(), TLiteralString::class))
            ->map(fn(TLiteralString $literal) => $literal->value);
    }
}
