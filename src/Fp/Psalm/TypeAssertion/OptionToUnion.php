<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use PhpParser\Node;
use Psalm\Type;
use Psalm\NodeTypeProvider;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use Fp\Functional\Option\Option;
use function Fp\Collection\first;

final class OptionToUnion implements PseudoAdtToUnion
{
    public function getUnion(NodeTypeProvider $type_provider, Node\Expr\MethodCall $from_assertion_method): Option
    {
        return PseudoAdtAtomicExtractor::extract($from_assertion_method->var, $type_provider, Option::class)
            ->flatMap(fn($generic_object) => first($generic_object->type_params))
            ->map(fn($type_param) => [
                new Type\Atomic\TGenericObject(Some::class, [$type_param]),
                new Type\Atomic\TNamedObject(None::class),
            ])
            ->map(fn($types) => new Type\Union($types));
    }
}
