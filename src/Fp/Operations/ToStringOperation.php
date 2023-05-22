<?php

declare(strict_types=1);

namespace Fp\Operations;

use Stringable;
use Throwable;

use function Fp\Collection\map;
use function Fp\Collection\mapKV;
use function Fp\Util\jsonEncode;

final class ToStringOperation
{
    public static function of(mixed $value): string
    {
        if ($value instanceof Throwable) {
            $message = self::of($value->getMessage());
            $exClass = $value::class;

            return $message !== '""'
                ? "{$exClass}({$message})"
                : "{$exClass}()";
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return match (get_debug_type($value)) {
            'null', 'int', 'bool', 'string', => jsonEncode($value),
            'float' => $value - ((int) $value) === 0.0 ? "{$value}.00" : "{$value}",
            'array' => self::arrayToStr($value),
            default => get_debug_type($value),
        };
    }

    private static function arrayToStr(array $items): string
    {
        $parts = array_is_list($items)
            ? map($items, fn($v) => self::of($v))
            : mapKV($items, fn($k, $v) => self::of($k) . ' => ' . self::of($v));

        return '[' . implode(', ', $parts) . ']';
    }
}
