<?php

declare(strict_types=1);

namespace Fp\Callable;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Fp\Functional\Option\Option;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\CtorFunctionReturnTypeProvider;

use function Fp\Collection\exists;
use function Fp\Collection\filterKV;
use function Fp\Collection\reindex;

/**
 * Makes class constructor from fqcn
 *
 * >>> ctor(Foo::class)
 * => Closure(int $a, bool $b = true, bool $c = true): Foo
 *
 * @template A
 *
 * @param class-string<A> $class
 * @return Closure(mixed...): A
 *
 * @see CtorFunctionReturnTypeProvider
 */
function ctor(string $class): Closure
{
    return function(mixed ...$args) use ($class) {
        // Assume we have class Foo(int $a, bool $b, bool $c)
        // Next code is runtime save: new Foo(...[1, true, false, 'not-necessary', 777])
        // i.e. 'not-necessary' and 777 will be ignored by PHP.
        if (array_is_list($args)) {
            /** @psalm-suppress MixedMethodCall */
            return new $class(...$args);
        }

        // But, code: new Foo(...[
        //   'a' => 1,
        //   'b' => true,
        //   'c' => false,
        //   'd' => 'not-necessary',
        //   'e' => 777,
        // ]);
        // is not runtime safe.
        //
        // For this reason, we must drop extra arguments.

        $availableParameters = Option::fromNullable((new ReflectionClass($class))->getConstructor())
            ->map(fn(ReflectionMethod $method) => $method->getParameters())
            ->getOrElse([]);

        $params = reindex($availableParameters, fn(ReflectionParameter $p) => $p->name);
        $hasVariadic = exists($params, fn(ReflectionParameter $p) => $p->isVariadic());

        $withoutExtraArgs = filterKV(
            collection: $args,
            predicate: fn($key) => array_key_exists($key, $params) || $hasVariadic,
        );

        /** @psalm-suppress MixedMethodCall */
        return new $class(...$withoutExtraArgs);
    };
}
