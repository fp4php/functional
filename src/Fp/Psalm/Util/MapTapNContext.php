<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Type\Atomic\TKeyedArray;

final class MapTapNContext
{
    public function __construct(
        public readonly MethodReturnTypeProviderEvent $event,
        public readonly TKeyedArray $func_args,
        public readonly TKeyedArray $current_args,
        public readonly bool $is_variadic,
        public readonly int $optional_count,
        public readonly int $required_count,
    ) {}
}
