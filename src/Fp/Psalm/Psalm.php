<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TLiteralFloat;
use Psalm\Type\Atomic\TLiteralInt;
use Psalm\Type\Atomic\TLiteralString;
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
     * @psalm-return Option<Atomic>
     */
    public static function getArgSingleAtomic(Arg $arg, StatementsSource $source): Option
    {
        return self::getArgUnion($arg, $source)
            ->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
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
     * @psalm-return Option<Atomic>
     */
    public static function getFirstArgSingleAtomic(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return self::getFirstArgUnion($event)->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
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
     * @psalm-return Option<NonEmptySet<int|float|string>>
     */
    public static function getUnionLiteralValues(Union $union): Option
    {
        $literalValues = ArrayList::collect($union->getLiteralStrings())
            ->appendedAll($union->getLiteralFloats())
            ->appendedAll($union->getLiteralInts())
            ->map(fn(TLiteralString|TLiteralFloat|TLiteralInt $literal) => $literal->value);

        return NonEmptyHashSet::collectOption($literalValues);
    }

    /**
     * @psalm-return Option<int|float|string>
     */
    public static function getUnionSingleLiteralValue(Union $union): Option
    {
        $someUnion = Option::some($union);

        return $someUnion
            ->filter(fn(Union $union) => $union->isSingleStringLiteral())
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleFloatLiteral()
                );
            })
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleIntLiteral()
                );
            })
            ->flatMap(fn(Union $type) => self::getUnionLiteralValues($type))
            ->map(fn(NonEmptySet $literals) => $literals->head());
    }
}
