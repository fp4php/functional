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
        public readonly string $refine_for,
        public readonly FunctionLike $predicate,
        public readonly Context $execution_context,
        public readonly StatementsAnalyzer $source,
        public readonly CollectionTypeParams $type_params,
    ) { }
}
