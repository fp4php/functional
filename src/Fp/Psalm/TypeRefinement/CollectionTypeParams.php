<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use Psalm\Type;

final class CollectionTypeParams
{
    public function __construct(
        public Type\Union $key_type,
        public Type\Union $val_type,
    ) { }
}
