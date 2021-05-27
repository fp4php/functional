<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\head;

/**
 * @internal
 */
class Psalm
{
    public static function nonEmptyListToList(TNonEmptyList $list): TList
    {
        return new TList($list->type_param);
    }

    public static function nonEmptyArrayToArray(TNonEmptyArray $array): TArray
    {
        return new TArray($array->type_params);
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::fromNullable($source->getNodeTypeProvider()->getType($arg->value));
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
    public static function getFirstArgCallableType(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg_type = yield self::getFirstArgType($event);
            return yield head(asList(
                $arg_type->getClosureTypes(),
                $arg_type->getCallableTypes()
            ));
        });
    }
}
