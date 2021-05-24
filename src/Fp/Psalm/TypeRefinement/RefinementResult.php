<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use Psalm\Type;

/**
 * @psalm-immutable
 */
final class RefinementResult
{
    public function __construct(
        public Type\Union $collection_key_type,
        public Type\Union $collection_value_type,
    ) { }
}
