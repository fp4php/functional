<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeAssertion;

use Psalm\Type;
use Psalm\NodeTypeProvider;
use PhpParser\Node;
use Fp\Functional\Option\Option;

interface PseudoAdtToUnion
{
    /**
     * @psalm-return Option<Type\Union>
     */
    public function getUnion(NodeTypeProvider $type_provider, Node\Expr\MethodCall $from_assertion_method): Option;
}
