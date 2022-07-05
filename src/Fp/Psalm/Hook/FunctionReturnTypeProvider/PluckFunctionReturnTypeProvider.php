<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTemplate;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
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
use ReflectionProperty;
use ReflectionUnionType;

use function Fp\Collection\at;
use function Fp\Collection\firstOf;
use function Fp\Collection\second;
use function Fp\Evidence\proveClassString;
use function Fp\Evidence\proveTrue;
use function Fp\Reflection\getReflectionProperty;

class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\pluck'),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);
            $arg_union = yield $args->head()->map(fn(CallArg $arg) => $arg->type);

            $target_union = yield self::getTypes($event);

            return new Union([
                new TArray([
                    GetCollectionTemplate::key($arg_union)->getOrElse(Type::getArrayKey()),
                    $target_union,
                ])
            ]);
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

            return yield getReflectionProperty($fqcn, $key)->toOption()
                ->map(fn($reflection) => self::getNamedTypes($reflection))
                ->flatMap(fn($named_types) => ArrayList::collect($named_types)
                    ->map(fn(ReflectionNamedType $nt) => $nt->getName())
                    ->reduce(fn(string $acc, $cur) => $acc . '|' . $cur))
                ->map(fn($type_string) => Type::parseString($type_string));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getTypesForObjectLikeArray(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);
            $key = yield self::getKey($event);

            return yield $args->head()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(fn(Union $type) => GetCollectionTemplate::value($type))
                ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TKeyedArray::class, $type))
                ->flatMap(fn(TKeyedArray $array) => at($array->properties, $key));
        });
    }

    /**
     * @psalm-return Option<class-string>
     */
    private static function getClassName(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $args = yield PsalmApi::$args->getCallArgs($event);

            return yield $args->head()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(fn(Union $type) => GetCollectionTemplate::value($type))
                ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TNamedObject::class, $type))
                ->flatMap(fn(TNamedObject $object) => proveClassString($object->value));
        });
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getKey(FunctionReturnTypeProviderEvent $event): Option
    {
        return second($event->getCallArgs())
            ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
            ->flatMap(fn(Union $key) => firstOf($key->getAtomicTypes(), TLiteralString::class))
            ->map(fn(TLiteralString $literal) => $literal->value);
    }

    /**
     * Returns property types by property reflection
     *
     * @param ReflectionProperty $property
     * @return list<ReflectionNamedType>
     */
    private static function getNamedTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();

        return match (true) {
            ($type instanceof ReflectionNamedType) => [$type],
            ($type instanceof ReflectionUnionType) => $type->getTypes(),
            default => [],
        };
    }
}
