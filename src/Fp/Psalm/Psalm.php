<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Collections\Collection;
use Fp\Collections\Map;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\head;
use function Fp\Evidence\proveTrue;

/**
 * Psalm helper methods
 *
 * @internal
 */
class Psalm
{
    /**
     * @psalm-return Option<Union>
     */
    public static function getArgUnion(Arg $arg, StatementsSource $source): Option
    {
        return Option::fromNullable($source->getNodeTypeProvider()->getType($arg->value));
    }

    /**
     * @psalm-return Option<FunctionLike>
     */
    public static function getArgFunctionLike(Arg $predicate_arg): Option
    {
        return Option::some($predicate_arg)
            ->map(fn(Arg $arg) => $arg->value)
            ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);
    }

    /**
     * @psalm-return Option<Atomic>
     */
    public static function getUnionSingeAtomic(Union $union): Option
    {
        return Option::do(function() use ($union) {
            $atomics = asList($union->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            return $atomics[0];
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getFirstArgUnion(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg = yield head($event->getCallArgs());
            return yield self::getArgUnion($arg, match(true) {
                $event instanceof MethodReturnTypeProviderEvent => $event->getSource(),
                $event instanceof FunctionReturnTypeProviderEvent => $event->getStatementsSource(),
            });
        });
    }

    /**
     * @psalm-return Option<TClosure|TCallable>
     */
    public static function getUnionFirstCallableType(Union $union): Option
    {
        return Option::do(function () use ($union) {
            return yield head(asList(
                $union->getClosureTypes(),
                $union->getCallableTypes()
            ));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getUnionValueTypeParam(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomic = yield Psalm::getUnionSingeAtomic($union);
            $someAtomic = Option::some($atomic);

            $filterTArray = $someAtomic
                ->filter(fn(Atomic $a) => $a instanceof TArray)
                ->map(fn(TArray $a) => $a->type_params[1]);

            $filterTList = $someAtomic
                ->filter(fn(Atomic $a) => $a instanceof TList)
                ->map(fn(TList $a) => $a->type_param);

            $filterTKeyedArray = $someAtomic
                ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
                ->map(fn(TKeyedArray $a) => $a->getGenericValueType());

            $filterTGenericObject = $someAtomic
                ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
                ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                    is_a($a->value, Seq::class, true) => $a->type_params[0],
                    is_a($a->value, Set::class, true) => $a->type_params[0],
                    is_a($a->value, Map::class, true) => $a->type_params[1],
                    is_a($a->value, NonEmptySeq::class, true) => $a->type_params[0],
                    is_a($a->value, NonEmptySet::class, true) => $a->type_params[0],
                    default => null
                }));

            return yield $filterTArray
                ->orElse(fn() => $filterTList)
                ->orElse(fn() => $filterTKeyedArray)
                ->orElse(fn() => $filterTGenericObject);
        });
    }
}
