<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeRefinement;

use Psalm\Type\Union;

/**
 * @psalm-immutable
 */
final class RefinementResult
{
    public function __construct(
        public Union $collection_key_type,
        public Union $collection_value_type,
    ) { }
}
