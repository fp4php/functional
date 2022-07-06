<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeRefinement;

use PhpParser\Node\FunctionLike;
use Psalm\Context;
use Psalm\Internal\Analyzer\StatementsAnalyzer;

final class RefinementContext
{
    public const FILTER_KEY_VALUE = 'filterKV';
    public const FILTER_VALUE = 'filter';

    /**
     * @psalm-param RefinementContext::FILTER_* $refine_for
     */
    public function __construct(
        public string $refine_for,
        public FunctionLike $predicate,
        public Context $execution_context,
        public StatementsAnalyzer $source,
    ) { }
}
