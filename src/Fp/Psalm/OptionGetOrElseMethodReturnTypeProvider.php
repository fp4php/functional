<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;
use SimpleXMLElement;

use function Fp\Collection\head;
use function Fp\Evidence\proveTrue;

class OptionGetOrElseMethodReturnTypeProvider implements PluginEntryPointInterface, MethodReturnTypeProviderInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }


    public static function getClassLikeNames(): array
    {
        return [Option::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function () use ($event) {
            yield proveTrue('getorelse' === $event->getMethodNameLowercase());
            $lower = yield self::getLowerBoundary($event);
            $upper = yield self::getUpperBoundary($event);
            return yield self::raiseToUpperBoundary($lower, $upper);
        })->get();
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function raiseToUpperBoundary(Union $lower, Union $upper): Option
    {
        return Option::do(function () use ($lower, $upper) {
            yield proveTrue(self::isEmptyArrayOrList($upper));
            yield proveTrue($lower->isArray());

            return $lower;
        });
    }

    public static function isEmptyArrayOrList(Union $type): bool
    {
        $emptyList = new Union([new TList(Type::getEmpty())]);

        return $type->equals(Type::getEmptyArray())
            || $type->equals($emptyList);
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getLowerBoundary(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function() use ($event) {
            $template_type_parameters = yield Option::of($event->getTemplateTypeParameters());
            return yield head($template_type_parameters);
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getUpperBoundary(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg = yield head($event->getCallArgs());
            return yield self::getArgType($arg, $event->getSource());
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::of($source->getNodeTypeProvider()->getType($arg->value));
    }
}
