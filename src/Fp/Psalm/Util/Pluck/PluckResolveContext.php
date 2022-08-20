<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Pluck;

use Psalm\CodeLocation;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Atomic\TNamedObject;

final class PluckResolveContext
{
    public function __construct(
        public readonly TLiteralString $key,
        public readonly TNamedObject|TKeyedArray $object,
        public readonly StatementsSource $source,
        public readonly CodeLocation $location,
    ) {}
}
