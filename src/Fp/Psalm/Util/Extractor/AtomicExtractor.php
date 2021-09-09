<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;


/**
 * @internal
 */
trait AtomicExtractor
{
    use SingleAtomicExtractor;
    use FirstAtomicExtractor;
}
