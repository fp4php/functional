<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use PhpParser\Node;
use Fp\Functional\Option\Option;
use Psalm\NodeTypeProvider;
use Psalm\Type;
use function Fp\Cast\asList;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

/**
 * @psalm-type NodeWithType
 *     = Node\Expr
 *     | Node\Name
 *     | Node\Stmt\Return_
 */
final class PseudoAdtAtomicExtractor
{
    /**
     * @psalm-param NodeWithType $node
     * @psalm-return Option<Type\Atomic\TGenericObject>
     */
    public static function extract(Node $node, NodeTypeProvider $provider, string $adtClass): Option
    {
        return Option::do(function() use ($node, $provider, $adtClass) {
            $type = yield Option::fromNullable($provider->getType($node));

            $atomics = asList($type->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            $generic_object = yield proveOf($atomics[0], Type\Atomic\TGenericObject::class);
            yield proveTrue($adtClass === $generic_object->value);

            return $generic_object;
        });
    }
}
