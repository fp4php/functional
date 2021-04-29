<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TI1
 * @psalm-template TO
 *
 * @psalm-param callable(TI1): TO $callback
 * @psalm-param TI1 $in1
 *
 * @psalm-return callable(): TO
 */
function curry1(callable $callback, mixed $in1): callable
{
    return fn() => $callback($in1);
}

/**
 * @psalm-template TI1
 * @psalm-template TI2
 * @psalm-template TO
 *
 * @psalm-param callable(TI1, TI2): TO $callback
 * @psalm-param TI1 $in1
 *
 * @psalm-return callable(TI2): TO
 */
function curry2(callable $callback, mixed $in1): callable
{
    return fn(mixed $in2) => $callback($in1, $in2);
}

/**
 * @psalm-template TI1
 * @psalm-template TI2
 * @psalm-template TI3
 * @psalm-template TO
 *
 * @psalm-param callable(TI1, TI2, TI3): TO $callback
 * @psalm-param TI1 $in1
 *
 * @psalm-return callable(TI2, TI3): TO
 */
function curry3(callable $callback, mixed $in1): callable
{
    return fn(mixed $in2, mixed $in3) => $callback($in1, $in2, $in3);
}

