<?php

declare(strict_types=1);

namespace Fp\Psalm;

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
    public static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::fromNullable($source->getNodeTypeProvider()->getType($arg->value));
    }

    /**
     * @psalm-return Option<Atomic>
     */
    public static function getSingeAtomic(Union $union): Option
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
    public static function getFirstArgType(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg = yield head($event->getCallArgs());
            return yield self::getArgType($arg, match(true) {
                $event instanceof MethodReturnTypeProviderEvent => $event->getSource(),
                $event instanceof FunctionReturnTypeProviderEvent => $event->getStatementsSource(),
            });
        });
    }

    /**
     * @psalm-return Option<TClosure|TCallable>
     */
    public static function getFirstCallableType(Union $union): Option
    {
        return Option::do(function () use ($union) {
            return yield head(asList(
                $union->getClosureTypes(),
                $union->getCallableTypes()
            ));
        });
    }

    /**
     * @psalm-return Option<FunctionLike>
     */
    public static function getPredicateFunction(Arg $predicate_arg): Option
    {
        return Option::some($predicate_arg)
            ->map(fn(Arg $arg) => $arg->value)
            ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);
    }
}
