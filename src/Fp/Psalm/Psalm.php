<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Union;

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
}
