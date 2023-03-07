<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Psalm\Type\Atomic\TKeyedArray;

final class ListChecker
{
    public static function isList(TKeyedArray $keyed): bool
    {
        if ($keyed->is_list) {
            if (count($keyed->properties) === 1
                && $keyed->fallback_params
                && $keyed->properties[0]->equals($keyed->fallback_params[1], true, true, false)
            ) {
                return $keyed->properties[0]->possibly_undefined;
            }
        }

        return false;
    }

    public static function isNonEmptyList(TKeyedArray $keyed): bool
    {
        if ($keyed->is_list) {
            if (count($keyed->properties) === 1
                && $keyed->fallback_params
                && $keyed->properties[0]->equals($keyed->fallback_params[1], true, true, false)
            ) {
                return !$keyed->properties[0]->possibly_undefined;
            }
        }

        return false;
    }
}
