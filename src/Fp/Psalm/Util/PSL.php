<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Fp\Psalm\Util\Extractor\AtomicExtractor;
use Fp\Psalm\Util\Extractor\LiteralExtractor;
use Fp\Psalm\Util\Extractor\NodeExtractor;
use Fp\Psalm\Util\Extractor\TypeParamExtractor;
use Fp\Psalm\Util\Extractor\UnionExtractor;

/**
 * Psalm helper methods
 *
 * @internal
 */
class PSL
{
    use TypeParamExtractor;
    use AtomicExtractor;
    use NodeExtractor;
    use UnionExtractor;
    use LiteralExtractor;
}
