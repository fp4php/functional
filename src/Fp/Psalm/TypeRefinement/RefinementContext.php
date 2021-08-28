<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use PhpParser\Node;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Internal\Analyzer\StatementsAnalyzer;

/**
 * @psalm-immutable
 */
final class RefinementContext
{
    public function __construct(
        public string $refine_for,
        public Node\Expr\Closure|Node\Expr\ArrowFunction $predicate,
        public Context $execution_context,
        public Codebase $codebase,
        public StatementsAnalyzer $source,
    ) { }
}
