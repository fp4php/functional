<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

/**
 * @internal
 */
trait TypeParamExtractor
{
    use TypeParamKeyExtractor;
    use TypeParamValueExtractor;
}
