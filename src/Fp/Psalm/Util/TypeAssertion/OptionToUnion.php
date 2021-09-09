<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeAssertion;

use PhpParser\Node\Expr\MethodCall;
use Psalm\NodeTypeProvider;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

use function Fp\Collection\first;

final class OptionToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Option::class)
            ->flatMap(fn($generic_object) => first($generic_object->type_params))
            ->map(fn($type_param) => [
                new TGenericObject(Some::class, [$type_param]),
                new TNamedObject(None::class),
            ])
            ->map(fn($types) => new Union($types));
    }
}
