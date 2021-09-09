<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Functional\Option\Option;
use Fp\Psalm\Util\PSL;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\head;
use function Fp\Evidence\proveTrue;

/**
 * @internal
 */
trait AtomicExtractor
{
    /**
     * @psalm-return Option<Atomic>
     */
    public static function getArgSingleAtomic(Arg $arg, StatementsSource $source): Option
    {
        return PSL::getArgUnion($arg, $source)
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
     * @psalm-return Option<Atomic>
     */
    public static function getFirstArgSingleAtomic(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return PSL::getFirstArgUnion($event)->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
    }

    /**
     * @psalm-return Option<TClosure|TCallable>
     */
    public static function getUnionFirstCallableType(Union $union): Option
    {
        return Option::do(function () use ($union) {
            return yield head(asList(
                $union->getClosureTypes(),
                $union->getCallableTypes()
            ));
        });
    }
}
