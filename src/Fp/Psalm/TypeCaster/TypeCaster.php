<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeCaster;

use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNonEmptyList;

class TypeCaster
{
    public static function nonEmptyListToList(TNonEmptyList $list): TList
    {
        return new TList($list->type_param);
    }

    public static function nonEmptyArrayToArray(TNonEmptyArray $array): TArray
    {
        return new TArray($array->type_params);
    }
}
