<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook;

use Closure;
use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Upcast\AtomicUpcast;
use Fp\Streams\Stream;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TInt;
use Psalm\Type\Atomic\TString;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TMixed;
use Psalm\Type\Atomic\TTemplateParam;
use Psalm\Type\Union;
use function Fp\Callable\ctor;

final class Playground implements AfterExpressionAnalysisInterface
{
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $input = new Union([
            new Atomic\TGenericObject(ArrayList::class, [
                Type::getInt(),
            ]),
        ]);

        $to = new Union([
            new TIterable([
                new Union([
                    new TTemplateParam('TK', new Union([new TMixed()]), 'plugin-scope'),
                ]),
                new Union([
                    new TTemplateParam('TV', new Union([new TMixed()]), 'plugin-scope'),
                ]),
            ]),
        ]);
        $_ = self::upcastUnion($input, $to);

        return null;
    }

    /**
     * @return Option<Union>
     */
    private static function upcastUnion(Union $input, Union $to): Option
    {
        $input_atomics = $input->getAtomicTypes();
        $to_atomics = $to->getAtomicTypes();

        if (count($input_atomics) !== count($to_atomics)) {
            return Option::none();
        }

        return ArrayList::collect($input_atomics)->zip($to_atomics)
            ->traverseOption(fn($tuple) => Stream::emits(AtomicUpcast::variants())
                ->filterMap(fn(Closure $upcaster) => Option::do(fn() => $upcaster(...$tuple)))
                ->firstElement())
            ->toArrayList()
            ->flatten()
            ->toNonEmptyList()
            ->map(ctor(Union::class));
    }
}
