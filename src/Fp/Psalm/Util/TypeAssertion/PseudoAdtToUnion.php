<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeAssertion;

use PhpParser\Node\Expr\MethodCall;
use Psalm\NodeTypeProvider;
use Fp\Functional\Option\Option;
use Psalm\Type\Union;

interface PseudoAdtToUnion
{
    /**
     * @psalm-return Option<Union>
     */
    public function getUnion(NodeTypeProvider $type_provider, MethodCall $from_assertion_method): Option;
}
