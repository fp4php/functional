<?php

declare(strict_types=1);

namespace Fp\Callable;

use ReflectionFunction;
use ReflectionParameter;

use function Fp\Collection\exists;
use function Fp\Collection\filterKV;
use function Fp\Collection\reindex;

/**
 * @template TCallable of callable
 *
 * @param TCallable $callable
 * @return TCallable
 */
function toSafeClosure(callable $callable): callable
{
    /** @var TCallable */
    return function(mixed ...$args) use ($callable): mixed {
        if (array_is_list($args)) {
            return $callable(...$args);
        }

        $params = reindex((new ReflectionFunction($callable(...)))->getParameters(), fn(ReflectionParameter $p) => $p->name);
        $hasVariadic = exists($params, fn(ReflectionParameter $p) => $p->isVariadic());

        $withoutExtraArgs = filterKV(
            collection: $args,
            predicate: fn($key) => array_key_exists($key, $params) || $hasVariadic,
        );

        return $callable(...$withoutExtraArgs);
    };
}
