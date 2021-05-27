<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use ReflectionNamedType;

use function Fp\Collection\first;
use function Fp\Collection\firstOf;
use function Fp\Collection\at;
use function Fp\Collection\head;
use function Fp\Collection\map;
use function Fp\Collection\second;
use function Fp\Evidence\proveClassString;
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
     * @todo TKeyedArray support
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
                $fqcn = yield self::getClassName($event);
                $key = yield self::getKey($event);
                $property_reflection = yield getReflectionProperty($fqcn, $key)->toOption();

                return implode('|', map(
                    getNamedTypes($property_reflection),
                    fn(ReflectionNamedType $nt) => $nt->getName())
                );
            })
            ->map(fn(string $property_type) => Type::parseString($property_type))
            ->map(fn(Union $property_type) => new Union([
                new TArray([Type::getArrayKey(), $property_type])
            ]))
            ->get();
    }

    /**
     * @psalm-return Option<class-string>
     */
    private static function getClassName(FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg = yield head($event->getCallArgs());
            $collection_type = yield Psalm::getArgType($arg, $event->getStatementsSource());
            $a = yield at($collection_type->getAtomicTypes(), 'array');

            $template_value_type = yield Option::fromNullable(match(true) {
                ($a instanceof TArray) => $a->type_params[1],
                ($a instanceof TKeyedArray) => $a->getGenericValueType(),
                default => null
            });

            $named_object = yield firstOf(
                $template_value_type->getAtomicTypes(),
                TNamedObject::class
            );

            return yield proveClassString($named_object->value);
        });
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getKey(FunctionReturnTypeProviderEvent $event): Option
    {
        return second($event->getCallArgs())
            ->flatMap(fn(Arg $arg): Option => Psalm::getArgType($arg, $event->getStatementsSource()))
            ->flatMap(fn(Union $key) => firstOf($key->getAtomicTypes(), TLiteralString::class))
            ->map(fn(TLiteralString $literal) => $literal->value);
    }
}
