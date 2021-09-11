<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Evidence\proveTrue;

/**
 * @internal
 */
trait SingleAtomicExtractor
{
    /**
     * @psalm-return Option<Atomic>
     */
    public static function getArgSingleAtomic(Arg $arg, StatementsSource $source): Option
    {
        return Psalm::getArgUnion($arg, $source)
            ->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
    }

    /**
     * @psalm-return Option<Atomic>
     */
    public static function getUnionSingeAtomic(Union $union): Option
    {
        return Option::do(function() use ($union) {
            $atomics = asList($union->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            return $atomics[0];
        });
    }

    /**
     * @template T
     * @psalm-param class-string<T> $fqcn
     * @psalm-return Option<T>
     */
    public static function getUnionSingleAtomicOf(Union $union, string $fqcn, bool $invariant = false): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf($fqcn, $invariant);
    }

    /**
     * @psalm-return Option<Atomic>
     */
    public static function getFirstArgSingleAtomic(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Psalm::getFirstArgUnion($event)->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
    }
}
