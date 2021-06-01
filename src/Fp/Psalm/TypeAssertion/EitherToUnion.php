<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use PhpParser\Node;
use Psalm\Type;
use Psalm\NodeTypeProvider;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;
use function Fp\Collection\first;
use function Fp\Collection\second;

/**
 * @psalm-type EitherTypeParams = array{
 *     left: Type\Union,
 *     right: Type\Union,
 * }
 */
final class EitherToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, Node\Expr\MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Either::class)
            ->flatMap(fn($generic_object) => self::getEitherTypeParams($generic_object))
            ->map(fn($type_params) => [
                new Type\Atomic\TGenericObject(Right::class, [$type_params['right']]),
                new Type\Atomic\TGenericObject(Left::class, [$type_params['left']]),
            ])
            ->map(fn($types) => new Type\Union($types));
    }

    /**
     * @psalm-return Option<EitherTypeParams>
     */
    private static function getEitherTypeParams(Type\Atomic\TGenericObject $generic_object): Option
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
