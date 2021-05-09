<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;
use SimpleXMLElement;

use function Fp\Function\Collection\head;
use function Fp\Function\Collection\map;
use function Fp\Function\Collection\tail;

class PartitionPlugin implements PluginEntryPointInterface, FunctionReturnTypeProviderInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            'fp\function\collection\partition',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        $source = $event->getStatementsSource();
        $args = $event->getCallArgs();
        $partition_count = count(tail($args));

        return head($args)
            ->flatMap(fn(Arg $head_arg) => self::getArgType($head_arg, $source))
            ->map(function(Union $head_arg_type) use ($partition_count) {
                $atomic_types = map(
                    $head_arg_type->getAtomicTypes(),
                    fn(Atomic $a) => match (true) {
                        ($a instanceof TKeyedArray) => new TArray([$a->getGenericKeyType(), $a->getGenericValueType()]),
                        ($a instanceof TList) => new TArray([Type::getArrayKey(), $a->type_param]),
                        default => $a
                    }
                );

                $upper_union = new Union([
                    ...array_values($atomic_types),
                    ...array_values($head_arg_type->getTemplateTypes()),
                    ...array_values($head_arg_type->getClosureTypes()),
                    ...array_values($head_arg_type->getCallableTypes()),
                ]);

                /** @var non-empty-array<string|int, Union> $partitions */
                $partitions = map(
                    range(1, $partition_count + 1),
                    fn() => clone $upper_union
                );

                return new Union([new TKeyedArray($partitions)]);
            })
            ->get();
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::of($source->getNodeTypeProvider()->getType($arg->value));
    }
}
