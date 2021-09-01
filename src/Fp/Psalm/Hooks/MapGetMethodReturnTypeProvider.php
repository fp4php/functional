<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Collections\HashMap;
use Fp\Collections\Map;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Psalm;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Collection\first;
use function Fp\Collection\second;
use function Fp\Evidence\proveTrue;

final class MapGetMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    private const ALLOWED_METHODS = [
        'get',
        '__invoke',
    ];

    public static function getClassLikeNames(): array
    {
        return [Map::class, HashMap::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Type\Union
    {
        $return_type = Option::do(function() use ($event) {
            yield proveTrue(in_array($event->getMethodNameLowercase(), self::ALLOWED_METHODS, true));

            $type_params         = yield Option::fromNullable($event->getTemplateTypeParameters());
            $key_type_param      = yield first($type_params);
            $value_type_param    = yield second($type_params);
            $arg_union           = yield Psalm::getFirstArgUnion($event);
            $type_param_literals = yield Psalm::getUnionLiteralValues($key_type_param);
            $arg_literal         = yield Psalm::getUnionSingleLiteralValue($arg_union);

            yield proveTrue($type_param_literals->contains($arg_literal));

            return new Union([
                new TGenericObject(Some::class, [$value_type_param])
            ]);
        });

        return $return_type->get();
    }
}
