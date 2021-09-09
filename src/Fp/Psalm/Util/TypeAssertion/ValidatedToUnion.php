<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeAssertion;

use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;
use PhpParser\Node\Expr\MethodCall;
use Psalm\Type;
use Psalm\NodeTypeProvider;
use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Collection\first;
use function Fp\Collection\second;

final class ValidatedToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Validated::class)
            ->flatMap(fn($generic_object) => self::getValidatedTypeParams($generic_object))
            ->map(fn($type_params) => [
                new TGenericObject(Valid::class, [$type_params['valid']]),
                new TGenericObject(Invalid::class, [$type_params['invalid']]),
            ])
            ->map(fn($types) => new Type\Union($types));
    }

    /**
     * @psalm-return Option<array{invalid: Union, valid: Union}>
     */
    private static function getValidatedTypeParams(TGenericObject $generic_object): Option
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
