<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;
use ReflectionNamedType;
use ReflectionProperty;
use SimpleXMLElement;

use function Fp\Function\Collection\first;
use function Fp\Function\Collection\firstInstanceOf;
use function Fp\Function\Collection\getByKey;
use function Fp\Function\Collection\map;
use function Fp\Function\Collection\second;
use function Fp\Function\Reflection\getNamedTypes;
use function Fp\Function\Reflection\getReflectionProperty;

class PluckPlugin implements PluginEntryPointInterface, FunctionReturnTypeProviderInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            'fp\function\collection\pluck',
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

                return implode('|', map(getNamedTypes(
                    $property_reflection),
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
     * @psalm-return Option<string>
     */
    private static function getClassName(FunctionReturnTypeProviderEvent $event): Option
    {
        return first($event->getCallArgs())
            ->flatMap(fn(Arg $arg): Option => self::getArgType($arg, $event->getStatementsSource()))
            ->flatMap(fn(Union $collection_type) => getByKey($collection_type->getAtomicTypes(), 'array'))
            ->map(fn(Atomic $a) => match(true) {
                ($a instanceof TArray) => $a->type_params[1],
                ($a instanceof TKeyedArray) => $a->getGenericValueType(),
                default => null
            })
            ->flatMap(fn(Union $template_value_type) => firstInstanceOf(
                $template_value_type->getAtomicTypes(),
                TNamedObject::class
            )
            ->map(fn(TNamedObject $named_object) => $named_object->value));
    }

    /**
     * @psalm-return Option<string>
     */
    private static function getKey(FunctionReturnTypeProviderEvent $event): Option
    {
        return second($event->getCallArgs())
            ->flatMap(fn(Arg $arg): Option => self::getArgType($arg, $event->getStatementsSource()))
            ->flatMap(fn(Union $key) => firstInstanceOf($key->getAtomicTypes(), TLiteralString::class))
            ->map(fn(TLiteralString $literal) => $literal->value);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::of($source->getNodeTypeProvider()->getType($arg->value));
    }
}
