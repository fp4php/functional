<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\GetCollectionTypeParams;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Issue\PossiblyUndefinedArrayOffset;
use Psalm\Issue\UndefinedPropertyFetch;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Storage\PropertyStorage;
use Psalm\Type;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Collection\at;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;

/**
 * @psalm-type PluckArgs = array{
 *     type: TNamedObject|TKeyedArray,
 *     key: TLiteralString
 * }
 */
class PluckFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [strtolower('Fp\Collection\pluck')];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(self::getArgsFromPluckFunctionCall(...))
            ->flatMap(fn(array $args) => self::getArrayValueTypeFromFunctionCall($args, $event))
            ->map(fn(Union $result) => match (true) {
                self::itWas(TNonEmptyList::class, $event) => new TNonEmptyList($result),
                self::itWas(TList::class, $event) => new TList($result),
                self::itWas(TNonEmptyArray::class, $event) => new TNonEmptyArray([self::getArrayKey($event), $result]),
                default => new TArray([self::getArrayKey($event), $result]),
            })
            ->map(fn(Type\Atomic $result) => new Union([$result]))
            ->get();
    }

    /**
     * @param PluckArgs $args
     * @return Option<Union>
     */
    private static function getArrayValueTypeFromFunctionCall(array $args, FunctionReturnTypeProviderEvent $event): Option
    {
        return self::getTypesForObject($args, $event)
            ->orElse(fn() => self::getTypesForObjectLikeArray($args, $event));
    }

    private static function getArrayKey(FunctionReturnTypeProviderEvent $event): Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => $args->head())
            ->flatMap(fn(CallArg $arg) => GetCollectionTypeParams::key($arg->type))
            ->getOrCall(fn() => Type::getArrayKey());
    }

    private static function itWas(string $class, FunctionReturnTypeProviderEvent $event): bool
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn(ArrayList $args) => $args->head())
            ->map(fn(CallArg $arg) => $arg->type)
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->map(fn(Type\Atomic $atomic) => $atomic instanceof $class)
            ->getOrElse(false);
    }

    /**
     * @param ArrayList<CallArg> $args
     * @return Option<PluckArgs>
     */
    private static function getArgsFromPluckFunctionCall(ArrayList $args): Option
    {
        return sequenceOption([
            'type' => $args->firstElement()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(GetCollectionTypeParams::value(...))
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->flatMap(fn($atomic) => proveOf($atomic, [TNamedObject::class, TKeyedArray::class])),
            'key' => $args->lastElement()
                ->map(fn(CallArg $arg) => $arg->type)
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->filterOf(TLiteralString::class),
        ]);
    }

    /**
     * @todo: Templated properties are not supported
     *
     * @param PluckArgs $args
     * @return Option<Union>
     */
    private static function getTypesForObject(array $args, FunctionReturnTypeProviderEvent $event): Option
    {
        return proveOf($args['type'], TNamedObject::class)
            ->flatMap(PsalmApi::$classlikes->getStorage(...))
            ->flatMap(fn(ClassLikeStorage $storage) => at($storage->properties, $args['key']->value)
                ->map(fn(PropertyStorage $property) => $property->type ?? Type::getMixed())
                ->orElse(fn() => self::undefinedPropertyIssue($storage->name, $args['key'], $event))
            );
    }

    /**
     * @return Option<never>
     */
    private static function undefinedPropertyIssue(string $class, TLiteralString $key, FunctionReturnTypeProviderEvent $event): Option
    {
        $source = $event->getStatementsSource();

        $issue = new UndefinedPropertyFetch(
            message: "Property '{$key->value}' is undefined",
            code_location: $event->getCodeLocation(),
            property_id: "{$class}::\${$key->value}",
        );

        IssueBuffer::accepts($issue, $source->getSuppressedIssues());
        return Option::none();
    }

    /**
     * @param PluckArgs $args
     * @return Option<Union>
     */
    private static function getTypesForObjectLikeArray(array $args, FunctionReturnTypeProviderEvent $event): Option
    {
        return proveOf($args['type'], TKeyedArray::class)->flatMap(
            fn(TKeyedArray $array) => at($array->properties, $args['key']->value)
                ->orElse(fn() => self::undefinedArrayKeyIssue($args['key'], $event)),
        );
    }

    /**
     * @return Option<never>
     */
    private static function undefinedArrayKeyIssue(TLiteralString $key, FunctionReturnTypeProviderEvent $event): Option
    {
        $source = $event->getStatementsSource();

        $issue = new PossiblyUndefinedArrayOffset(
            message: "Array key '{$key->value}' is undefined",
            code_location: $event->getCodeLocation(),
        );

        IssueBuffer::accepts($issue, $source->getSuppressedIssues());
        return Option::none();
    }
}
