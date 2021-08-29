<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use Fp\Psalm\Psalm;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Return_;
use Fp\Functional\Option\Option;
use Psalm\NodeTypeProvider;
use Psalm\Type\Atomic\TGenericObject;

use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class PseudoAdtAtomicExtractor
{
    /**
     * @psalm-return Option<TGenericObject>
     */
    public static function extract(Expr|Name|Return_ $node, NodeTypeProvider $provider, string $adtClass): Option
    {
        return Option::do(function() use ($node, $provider, $adtClass) {
            $generic_object = yield Option::fromNullable($provider->getType($node))
                ->flatMap(fn($type) => Psalm::getSingeAtomic($type))
                ->flatMap(fn($atomic) => proveOf($atomic, TGenericObject::class));

            yield proveTrue($adtClass === $generic_object->value);

            return $generic_object;
        });
    }
}
