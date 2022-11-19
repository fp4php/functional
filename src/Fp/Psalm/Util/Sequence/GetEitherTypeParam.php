<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Sequence;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic;
use Psalm\Type\Union;

use function Fp\Collection\at;

final class GetEitherTypeParam
{
    public const GET_LEFT = 0;
    public const GET_RIGHT = 1;

    /**
     * @return Option<Union>
     */
    public static function left(Union $type): Option
    {
        return self::from($type, self::GET_LEFT);
    }

    /**
     * @return Option<Union>
     */
    public static function right(Union $type): Option
    {
        return self::from($type, self::GET_RIGHT);
    }

    /**
     * @param self::GET_* $idx
     * @return Option<Union>
     */
    public static function from(Union $type, int $idx): Option
    {
        return PsalmApi::$types->asSingleAtomic($type)
            ->flatMap(fn(Atomic $atomic) => match (true) {
                $atomic instanceof TGenericObject => Option::some($atomic)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Either::class)
                    ->flatMap(fn(TGenericObject $option) => at($option->type_params, $idx)),
                $atomic instanceof TClosure => Option::fromNullable($atomic->return_type)
                    ->flatMap(fn(Union $t) => self::from($t, $idx)),
                default => Option::none(),
            });
    }
}
