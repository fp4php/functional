<?php

declare(strict_types=1);

namespace Fp\Operations;

use Stringable;
use Throwable;
use function Fp\Collection\map;
use function Fp\Collection\mapKV;

final class ToStringOperation
{
    public static function of(mixed $value): string
    {
        if ($value instanceof Throwable) {
            $message = $value->getMessage();
            $exClass = $value::class;

            return empty($message) ? "{$exClass}()" : "{$exClass}('{$message}')";
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return match (get_debug_type($value)) {
            'null' => 'null',
            'int', 'float' => "{$value}",
            'bool' => $value ? 'true' : 'false',
            'string' => "'" . str_replace("'", "\'", $value) . "'",
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
