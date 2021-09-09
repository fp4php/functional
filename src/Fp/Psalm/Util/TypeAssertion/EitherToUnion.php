<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeAssertion;

use PhpParser\Node\Expr\MethodCall;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use Psalm\NodeTypeProvider;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;
use function Fp\Collection\first;
use function Fp\Collection\second;

final class EitherToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Either::class)
            ->flatMap(fn($generic_object) => self::getEitherTypeParams($generic_object))
            ->map(fn($type_params) => [
                new TGenericObject(Right::class, [$type_params['right']]),
                new TGenericObject(Left::class, [$type_params['left']]),
            ])
            ->map(fn($types) => new Union($types));
    }

    /**
     * @psalm-return Option<array{left: Union, right: Union}>
     */
    private static function getEitherTypeParams(TGenericObject $generic_object): Option
    {
        return Option::do(function() use ($generic_object) {
            $left = yield first($generic_object->type_params);
            $right = yield second($generic_object->type_params);

            return [
                'left' => $left,
                'right' => $right,
            ];
        });
    }
}
