<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use PhpParser\Node\Arg;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Internal\Analyzer\StatementsAnalyzer;

/**
 * @psalm-immutable
 */
final class RefinementContext
{
    public function __construct(
        public Arg $predicate_arg,
        public Context $execution_context,
        public Codebase $codebase,
        public StatementsAnalyzer $source,
    ) { }
}
