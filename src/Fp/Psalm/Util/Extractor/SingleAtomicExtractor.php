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
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
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
     * @psalm-return Option<Atomic>
     */
    public static function getFirstArgSingleAtomic(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Psalm::getFirstArgUnion($event)->flatMap(fn(Union $union) => self::getUnionSingeAtomic($union));
    }

    /**
     * @psalm-return Option<TGenericObject>
     */
    public static function getUnionTGenericObjectSingleAtomic(Union $union): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf(TGenericObject::class, true);
    }

    /**
     * @psalm-return Option<TIterable>
     */
    public static function getUnionTIterableSingleAtomic(Union $union): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf(TIterable::class, true);
    }

    /**
     * @psalm-return Option<TArray>
     */
    public static function getUnionTArrayTypeSingleAtomic(Union $union): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf(TArray::class, true);
    }

    /**
     * @psalm-return Option<TKeyedArray>
     */
    public static function getUnionTKeyedArraySingleAtomic(Union $union): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf(TKeyedArray::class, true);
    }

    /**
     * @psalm-return Option<TList>
     */
    public static function getUnionTListSingleAtomic(Union $union): Option
    {
        return self::getUnionSingeAtomic($union)->filterOf(TList::class, true);
    }
}
