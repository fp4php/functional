<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node\Arg;
use Psalm\CodeLocation;
use Psalm\Internal\Type\Comparator\CallableTypeComparator;
use Psalm\Issue\InvalidArgument;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;

use function Fp\Cast\asList;
use function Fp\Collection\filterNotNull;
use function Fp\Collection\head;
use function Fp\Collection\map;
use function Fp\Collection\tail;

final class PartialFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Callable\partial'),
            strtolower('Fp\Callable\partialLeft'),
            strtolower('Fp\Callable\partialRight'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return head($event->getCallArgs())
            ->flatMap(fn(Arg $head_arg) => PsalmApi::$args->getArgType($event, $head_arg))
            ->flatMap(fn(Union $head_arg_type) => head(array_merge(
                $head_arg_type->getClosureTypes(),
                $head_arg_type->getCallableTypes(),
                filterNotNull(map(
                    collection: asList($head_arg_type->getAtomicTypes()),
                    callback: fn(Atomic $atomic) => CallableTypeComparator::getCallableFromAtomic(
                        codebase: $event->getStatementsSource()->getCodebase(),
                        input_type_part: $atomic
                    )
                ))
            )))
            ->map(function (TClosure|TCallable $closure_type) use ($event) {
                $is_partial_right = str_ends_with($event->getFunctionId(), 'right');
                $closure_type_copy = clone $closure_type;
                $closure_params = $closure_type_copy->params ?? [];
                $tail_args = tail($event->getCallArgs());

                self::assertValidArgs(
                    event: $event,
                    callable: $closure_type_copy,
                    args: $tail_args,
                    is_partial_right: $is_partial_right
                );

                $args_tail_size = count($tail_args);

                if (0 === $args_tail_size) {
                    return new Union([$closure_type_copy]);
                }

                $free_params = $is_partial_right
                    ? array_slice($closure_params, 0, -$args_tail_size)
                    : array_slice($closure_params, $args_tail_size);

                $closure_type_copy->params = $free_params;

                return new Union([$closure_type_copy]);
            })
            ->get();
    }

    /**
     * @psalm-param array<array-key, Arg> $args
     */
    private static function assertValidArgs(
        FunctionReturnTypeProviderEvent $event,
        TClosure|TCallable $callable,
        array $args,
        bool $is_partial_right
    ): void
    {
        $source = $event->getStatementsSource();
        $codebase = $source->getCodebase();

        $args_list = array_values($args);
        $params_list = $is_partial_right
            ? array_reverse($callable->params ?? [])
            : $callable->params ?? [];

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args_list[$i] ?? null;
            $param = $params_list[$i] ?? null;

            if (!isset($arg, $param)) {
                continue;
            }

            $param_type = $param->type ?? Type::getMixed();
            $arg_type = PsalmApi::$args->getArgType($event, $arg);

            if ($arg_type->isNone()) {
                continue;
            }

            $is_subtype_of = $codebase->isTypeContainedByType($arg_type->get(), $param_type);

            if ($is_subtype_of) {
                continue;
            }

            self::issueInvalidArgument(
                function_id: $event->getFunctionId(),
                code_location: $event->getCodeLocation(),
                expected_type: (string) $param_type
            );
        }
    }


    private static function issueInvalidArgument(string $function_id, CodeLocation $code_location, string $expected_type): void
    {
        $issue = new InvalidArgument(
            message: sprintf('argument should be of type %s', $expected_type),
            code_location: $code_location,
            function_id: $function_id
        );

        IssueBuffer::accepts($issue);
    }
}
