<?php

namespace Fp\Psalm\Util;

use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TKeyedArray;
use function Fp\Collection\dropRight;
use function Fp\Collection\init;
use function Fp\Collection\last;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

enum MapTapNContextEnum
{
    case Shape;
    case Tuple;

    public function tuneForOptionalAndVariadicParams(MapTapNContext $ctx): TKeyedArray
    {
        if ($this === MapTapNContextEnum::Shape) {
            return $ctx->func_args;
        }

        return $this->tuneForOptional(
            func_args: $ctx->is_variadic
                ? $this->tuneForVariadic($ctx->func_args, $ctx->current_args)
                : $ctx->func_args,
            drop_length: $ctx->optional_count > 0
                ? $ctx->required_count + $ctx->optional_count - count($ctx->current_args->properties)
                : 0,
        );
    }

    private function tuneForOptional(TKeyedArray $func_args, int $drop_length): TKeyedArray
    {
        $propsOption = proveNonEmptyList(dropRight($func_args->properties, $drop_length));

        $cloned = clone $func_args;
        $cloned->properties = $propsOption->getOrElse($cloned->properties);

        return $cloned;
    }

    private function tuneForVariadic(TKeyedArray $func_args, TKeyedArray $current_args): TKeyedArray
    {
        $func_args_count = count($func_args->properties);
        $current_args_count = count($current_args->properties);

        $propsOption = match (true) {
            // There are variadic args: extend type with variadic param type
            $current_args_count > $func_args_count => last($func_args->properties)
                ->map(fn($last) => [
                    ...$func_args->properties,
                    ...array_fill(0, $current_args_count - $func_args_count, $last),
                ]),

            // No variadic args: remove variadic param from type
            $current_args_count < $func_args_count => proveNonEmptyArray(init($func_args->properties)),

            // Exactly one variadic arg: leave as is
            default => Option::none(),
        };

        $cloned = clone $func_args;
        $cloned->properties = $propsOption->getOrElse($cloned->properties);

        return $cloned;
    }
}
