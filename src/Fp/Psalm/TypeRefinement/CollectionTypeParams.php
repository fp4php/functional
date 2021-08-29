<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use Psalm\Type\Union;

final class CollectionTypeParams
{
    public function __construct(
        public Union $key_type,
        public Union $val_type,
    ) { }
}
