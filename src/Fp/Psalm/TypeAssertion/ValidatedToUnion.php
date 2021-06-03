<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;
use PhpParser\Node;
use Psalm\Type;
use Psalm\NodeTypeProvider;
use Fp\Functional\Option\Option;
use function Fp\Collection\first;
use function Fp\Collection\second;

/**
 * @psalm-type ValidatedTypeParams = array{
 *     invalid: Type\Union,
 *     valid: Type\Union,
 * }
 */
final class ValidatedToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, Node\Expr\MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Validated::class)
            ->flatMap(fn($generic_object) => self::getValidatedTypeParams($generic_object))
            ->map(fn($type_params) => [
                new Type\Atomic\TGenericObject(Valid::class, [$type_params['valid']]),
                new Type\Atomic\TGenericObject(Invalid::class, [$type_params['invalid']]),
            ])
            ->map(fn($types) => new Type\Union($types));
    }

    /**
     * @psalm-return Option<ValidatedTypeParams>
     */
    private static function getValidatedTypeParams(Type\Atomic\TGenericObject $generic_object): Option
    {
        return Option::do(function() use ($generic_object) {
            $left = yield first($generic_object->type_params);
            $right = yield second($generic_object->type_params);

            return [
                'invalid' => $left,
                'valid' => $right,
            ];
        });
    }
}
